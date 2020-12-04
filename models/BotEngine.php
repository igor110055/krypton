<?php
namespace app\models;

use app\interfaces\ExchangeInterface;
use app\models\Api\Binance;
use app\utils\BittrexParser;
use Yii;
use app\models\Alert;
use app\models\Api\Bittrex;
use app\models\PendingOrder;
use app\models\Order;

class BotEngine
{
    public $exchanges = ['Bittrex', 'Binance'];

    private $api;
    private $marketLastBids;

    public function __construct()
    {
        $this->api = new Bittrex();

        $this->Binance = new Binance();
        $this->Bittrex = new Bittrex();
    }

    public function checkAlerts()
    {
        if (!$this->marketLastBids) {
            return false;
        }

        $alerts = Alert::findAll([
            'is_active' => 1
        ]);

        foreach ($alerts as $alert) {

            $alertData = $alert->getAttributes();
            $actualMarketPrice = $this->marketLastBids['Bittrex'][$alertData['market']];

            switch ($alertData['condition']) {
                case 'COND_MORE':
                    if ($actualMarketPrice >= $alertData['price']) {
                        $this->sendAlertMail($alertData, $actualMarketPrice);
                        $alert->is_active = 0;
                        $alert->save();
                    }
                    break;
                case 'COND_LESS':
                    if ($actualMarketPrice <= $alertData['price']) {
                        $this->sendAlertMail($alertData, $actualMarketPrice);
                        $alert->is_active = 0;
                        $alert->save();
                    }
                    break;
            }
        }
    }

    public function checkPendingOrders()
    {
        if (!$this->marketLastBids) {
            return false;
        }

        $pendingOrders = PendingOrder::find()->all();

        foreach ($pendingOrders as $pendingOrder) {

            $currentMarketPrice = $this->marketLastBids[$pendingOrder->exchange][$pendingOrder->market];

            switch ($pendingOrder->condition) {
                case 'COND_MORE':
                    if ($currentMarketPrice >= $pendingOrder->price) {
                        $this->placeOrder($pendingOrder);
                    }
                    break;
                case 'COND_LESS':
                    if ($currentMarketPrice <= $pendingOrder->price) {
                        $this->placeOrder($pendingOrder);
                    }
                    if ($pendingOrder && $pendingOrder->type == 'SELL') {
                        $this->checkRisingStopLoss($pendingOrder, $currentMarketPrice);
                    }
                    break;
            }
        }
    }

    public function getExchangeClient(string $exchange): ExchangeInterface
    {
        return $this->$exchange;
    }

    private function checkRisingStopLoss(PendingOrder $pendingOrder, $currentMarketPrice)
    {
        $uuid = $pendingOrder->uuid;

        /** @var Order $order */
        $order = Order::find()->where(['uuid' => $uuid])->one();

        if ($currentMarketPrice > $order->price) {
            $diff = $currentMarketPrice - $order->price;
            $percentDiff = round($diff / $order->price * 100, 2);

            $newStopLoss = $currentMarketPrice - $currentMarketPrice / 100;
            $stopLossDiff = $newStopLoss - $order->stop_loss;
            $stopLossPercentDiff = round($stopLossDiff / $order->stop_loss * 100, 2);

            if ($percentDiff > 2 && $stopLossPercentDiff > 2) {

                $pendingOrder->price = $newStopLoss;
                $pendingOrder->value = $newStopLoss * $pendingOrder->quantity;
                $pendingOrder->save();

                $order->stop_loss = $newStopLoss;
                $order->save();

                $this->stopLossIncreasedMail($order);
            }
        }
    }

    public function placeOrder(PendingOrder $pendingOrder)
    {
        $api = $this->getExchangeClient($pendingOrder->exchange);
        $actualTicker = $api->getCurrentPrice($pendingOrder->market);

        switch ($pendingOrder->type) {
            case 'BUY':

                if ($pendingOrder->transaction_type == $pendingOrder::TRANSACTION_BEST) {
                    $offerPrice = $bestOffer = $actualTicker['ask'];
                } else {
                    $offerPrice = $pendingOrder->price;
                }

                $result = $api->placeBuyOrder($pendingOrder->market, $pendingOrder->quantity, $offerPrice);
                if ($result['success']) {

                    $this->sendPlaceOrderMail($pendingOrder);

                    $order = new Order();

                    $order->uuid = (string)$result['orderId'];
                    $order->exchange = $pendingOrder->exchange;
                    $order->market = $pendingOrder->market;
                    $order->quantity = $pendingOrder->quantity;
                    $order->price = $pendingOrder->price;
                    $order->value = $pendingOrder->price * $pendingOrder->quantity;
                    $order->type = $pendingOrder->type;
                    $order->stop_loss = $pendingOrder->stop_loss;
                    $order->take_profit = $pendingOrder->take_profit;
                    $order->status = Order::STATUS_OPEN;
                    $order->transaction_type = $pendingOrder->transaction_type;

                    $order->save();
                    $pendingOrder->delete();
                } else {
                    $this->errorMail($pendingOrder, 'Error with pending order: ' . $pendingOrder->id);
                }

                break;
            case 'SELL':
//                $bestOffer = $actualTicker['bid'];
                if ($pendingOrder->transaction_type == $pendingOrder::TRANSACTION_BEST) {
                    $offerPrice = $bestOffer = $actualTicker['bid'];
                } else {
                    $offerPrice = $pendingOrder->price;
                }
                $result = $api->placeSellOrder($pendingOrder->market, $pendingOrder->quantity, $offerPrice);
                if ($result['success']) {
                    $this->sendPlaceOrderMail($pendingOrder);
                    $uuid = $pendingOrder->uuid;
                    $pendingOrder->delete();
                    $oppositeOrder = PendingOrder::find()->where(['uuid' => $uuid])->one();
                    if ($oppositeOrder) {
                        $oppositeOrder->delete();
                    }
                    $order = Order::find()->where(['uuid' => $uuid])->one();
                    if ($order) {
                        $order->status = Order::STATUS_DONE;
                        $order->sell_price = $bestOffer;
                        $order->sell_value = $pendingOrder->quantity * $bestOffer;
                        $order->sell_uuid = $result['orderId'];
                        $order->sell_placed = date('Y-m-d H:i:s');
                        $order->save();
                    }
                } else {
                    $this->errorMail($pendingOrder, 'Error with pending order: ' . $pendingOrder->id);
                }
                break;
        }
    }

    public function sellOrder(Order $order)
    {
        $api = $this->getExchangeClient($order->exchange);
        $return = [];
        $currentTicker = $api->getTickerFormatted($order->market);
        if ($order->transaction_type == $order::TRANSACTION_BEST) {
            $bestOffer = $currentTicker['Bid'];
        } else {
            $bestOffer = $currentTicker['Last'];
        }
        $result = $api->placeSellOrder($order->market, $order->quantity, $bestOffer);
        if ($result['success']) {
            $uuid = $order->uuid;
            $pendingOrders = PendingOrder::find()->where(['uuid' => $uuid])->all();
            if (count($pendingOrders) > 0) {
                foreach ($pendingOrders as $pendingOrder) {
                    $pendingOrder->delete();
                }
            }
            $order->status = Order::STATUS_DONE;
            $order->sell_price = $bestOffer;
            $order->sell_value = $pendingOrder->quantity * $bestOffer;
            $order->sell_uuid = $result['result']['uuid'];
            $order->sell_placed = date('Y-m-d H:i:s');
            $order->save();
            $return['success'] = true;
            $return['msg'] = '';
        } else {
            $return['success'] = false;
            $return['msg'] = $result['message'];
        }

        return $return;
    }

    public function checkOpenOrders()
    {
        $orders = Order::findAll([
            'status' => Order::STATUS_OPEN
        ]);

        if (count($orders) == 0) {
            return false;
        }

        $openOrdersUuids = [];

        $bittrexOpenOrdersResult = $this->getExchangeClient('Bittrex')->getOpenOrders();
        if($bittrexOpenOrdersResult['success']) {
            if(count($bittrexOpenOrdersResult['result']) > 0) {
                foreach ($bittrexOpenOrdersResult['result'] as $openOrder){
                    $openOrdersUuids[] = $openOrder['OrderUuid'];
                }
            }
        }
        $binanceOpenOrders = $this->getExchangeClient('Binance')->getOpenOrders();
        if($binanceOpenOrders && count($binanceOpenOrders) > 0) {
            foreach($binanceOpenOrders as $openOrder) {
                $openOrdersUuids[] = (string)$openOrder['orderId'];
            }
        }

        foreach ($orders as $order) {
            if (!in_array($order->uuid, $openOrdersUuids)) {
                //sprawdzenie ile się kupiło i aktualizacja ilości
                $order->status = Order::STATUS_CLOSED;
                $order->save();
                $this->sendRealizedOrderMail($order);
            }
        }
    }

    public function createPendingOrdersForClosedOrders()
    {
        $orders = Order::findAll([
            'status' => Order::STATUS_CLOSED
        ]);

        foreach ($orders as $order) {

            if ($order->take_profit) {
                $orderEarn = new PendingOrder();
                $orderEarn->exchange = $order->exchange;
                $orderEarn->market = $order->market;
                $orderEarn->quantity = $order->quantity;
                $orderEarn->price = $order->take_profit;
                $orderEarn->value = $order->take_profit * $order->quantity;
                $orderEarn->type = 'SELL';
                $orderEarn->condition = 'COND_MORE';
                $orderEarn->uuid = $order->uuid;
                $orderEarn->transaction_type = $order->transaction_type;
                $orderEarn->save();
            }

            if ($order->stop_loss) {
                $orderLoss = new PendingOrder();
                $orderLoss->exchange = $order->exchange;
                $orderLoss->market = $order->market;
                $orderLoss->quantity = $order->quantity;
                $orderLoss->price = $order->stop_loss;
                $orderLoss->value = $order->stop_loss * $order->quantity;
                $orderLoss->type = 'SELL';
                $orderLoss->condition = 'COND_LESS';
                $orderLoss->uuid = $order->uuid;
                $orderLoss->transaction_type = $order->transaction_type;
                $orderLoss->save();
            }

            $order->status = Order::STATUS_PROCESSED;
            $order->save();
        }
    }

    public function sendAlertMail($alertData, $price)
    {
        switch ($alertData['condition']) {
            case 'COND_MORE':
                $subject = 'Alert '.$alertData['market'].' większy niż '.number_format($alertData['price'], 8);
                break;
            case 'COND_LESS':
                $subject = 'Alert '.$alertData['market'].' mniejszy niż '.number_format($alertData['price'], 8);
                break;
        }
        $body = 'Aktualna cena: '.number_format($price, 8)."\n";
        $body .= 'Info: '.$alertData['message']."\n";

        $mail = Yii::$app->mailer->compose();
        $mail->setFrom('admin@wales.usermd.net')
            ->setTo('leszek.walszewski@gmail.com')
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }

    public function sendPlaceOrderMail(PendingOrder $pendingOrder)
    {
        $value = $pendingOrder->price * $pendingOrder->quantity;
        $value = round($value, 2);

        $subject = '[' . $pendingOrder->market . '] ' . $pendingOrder->type  . ': price: ' . number_format($pendingOrder->price, 8) . ' | val: ' . $value;

        if ($pendingOrder->type == 'SELL') {
            switch ($pendingOrder->condition) {
                case 'COND_MORE':
                    $body = 'Order placed. Earn :)';
                    break;
                case 'COND_LESS':
                    $body = 'Order placed. Loss :(';
                    break;
            }
        } else {
            $body = 'Order placed.';
        }

        $mail = Yii::$app->mailer->compose();
        $mail->setFrom('admin@wales.usermd.net')
            ->setTo('leszek.walszewski@gmail.com')
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }

    public function sendRealizedOrderMail(Order $order)
    {
        $subject = '[' . $order->market . '] ' . $order->type  . ': price: ' . number_format($order->price, 8) . ' | val: ' . $order->value;

        $body = 'Order closed.';

        $mail = Yii::$app->mailer->compose();
        $mail->setFrom('admin@wales.usermd.net')
            ->setTo('leszek.walszewski@gmail.com')
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }

    public function stopLossIncreasedMail(Order $order)
    {
        $subject = '[' . $order->market . '] Stop Loss increased. Val: ' . number_format($order->stop_loss, 8);

        $body = 'Stop Loss increased.';

        $mail = Yii::$app->mailer->compose();
        $mail->setFrom('admin@wales.usermd.net')
            ->setTo('leszek.walszewski@gmail.com')
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }

    public function prepareCurrentPrices()
    {
        $marketSummaries = $this->Bittrex->getMarketSummaries();
        if (!$marketSummaries['success']) {
            return false;
        }

        $this->marketLastBids['Bittrex'] = BittrexParser::getPricesFromSummaries($marketSummaries);
        $this->marketLastBids['Binance'] = $this->Binance->getPricesFormatted();
    }

    public function errorMail(PendingOrder $pendingOrder, $msg)
    {
        $subject = 'Error ' . $pendingOrder->type . ' ' . $pendingOrder->market;

        $body = 'Pending order ID: ' . $pendingOrder->id . "\n";
        $body .= $msg;

        $mail = Yii::$app->mailer->compose();
        $mail->setFrom('admin@wales.usermd.net')
            ->setTo('leszek.walszewski@gmail.com')
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }

    public function getMarketLastBids()
    {
        return $this->marketLastBids;
    }

    public function getExchangesSummaries(): array
    {
        $summary['Binance'] = $this->Binance->getBalanceSummary();
        $summary['Bittrex'] = $this->Bittrex->getBalanceSummary();

        return $summary;
    }
}