<?php
namespace app\models;

use app\utils\BittrexParser;
use Yii;
use app\models\Alert;
use app\models\Api\Bittrex;
use app\models\PendingOrder;
use app\models\Order;

class BotEngine
{
    private $api;
    private $marketLastBids;

    public function __construct()
    {
        $this->api = new Bittrex();
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
            $actualMarketPrice = $this->marketLastBids[$alertData['market']];

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

            $actualMarketPrice = $this->marketLastBids[$pendingOrder->market];

            switch ($pendingOrder->condition) {
                case 'COND_MORE':
                    if ($actualMarketPrice >= $pendingOrder->price) {
                        $this->placeOrder($pendingOrder);
                    }
                    break;
                case 'COND_LESS':
                    if ($actualMarketPrice <= $pendingOrder->price) {
                        $this->placeOrder($pendingOrder);
                    }
                    break;
            }
        }
    }

    public function placeOrder(PendingOrder $pendingOrder)
    {
        $actualTicker = $this->api->getTicker($pendingOrder->market);

        switch ($pendingOrder->type) {
            case 'BUY':
                $bestOffer = $actualTicker['result']['Ask'];
                $result = $this->api->placeBuyOrder($pendingOrder->market, $pendingOrder->quantity, $bestOffer);
                if ($result['success']) {

                    $this->sendPlaceOrderMail($pendingOrder);

                    $order = new Order();

                    $order->uuid = $result['result']['uuid'];
                    $order->market = $pendingOrder->market;
                    $order->quantity = $pendingOrder->quantity;
                    $order->price = $bestOffer;
                    $order->value = $bestOffer * $pendingOrder->quantity;
                    $order->type = $pendingOrder->type;
                    $order->stop_loss = $pendingOrder->stop_loss;
                    $order->take_profit = $pendingOrder->take_profit;
                    $order->status = Order::STATUS_OPEN;

                    $order->save();
                    $pendingOrder->delete();
                } else {
                    $this->errorMail($pendingOrder, $result['message']);
                }

                break;
            case 'SELL':
                $bestOffer = $actualTicker['result']['Bid'];
                $result = $this->api->placeSellOrder($pendingOrder->market, $pendingOrder->quantity, $bestOffer);
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
                        $order->sell_uuid = $result['result']['uuid'];
                        $order->sell_placed = date('Y-m-d H:i:s');
                        $order->save();
                    }
                } else {
                    $this->errorMail($pendingOrder, $result['message']);
                }
                break;
        }

        $this->checkOpenOrders();
    }

    public function sellOrder(Order $order)
    {
        $return = [];
        $actualTicker = $this->api->getTicker($order->market);
        $bestOffer = $actualTicker['result']['Bid'];
        $result = $this->api->placeSellOrder($order->market, $order->quantity, $bestOffer);
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

    public function checkOpenOrders($market = null)
    {
        $orders = Order::findAll([
            'status' => Order::STATUS_OPEN
        ]);

        if (count($orders) == 0) {
            return false;
        }

        $result = $this->api->getOpenOrders($market);
        $openOrdersUuids = [];

        if ($result['success']) {
            if(count($result['result']) > 1) {
                foreach ($result['result'] as $openOrder){
                    $openOrdersUuids[] = $openOrder['OrderUuid'];
                }
            }
        }

        foreach ($orders as $order) {
            if (!in_array($order->uuid, $openOrdersUuids)) {
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
            $orderEarn = new PendingOrder();
            $orderEarn->market = $order->market;
            $orderEarn->quantity = $order->quantity;
            $orderEarn->price = $order->take_profit;
            $orderEarn->value = $order->take_profit * $order->quantity;
            $orderEarn->type = 'SELL';
            $orderEarn->condition = 'COND_MORE';
            $orderEarn->uuid = $order->uuid;
            $orderEarn->save();

            $orderLoss = new PendingOrder();
            $orderLoss->market = $order->market;
            $orderLoss->quantity = $order->quantity;
            $orderLoss->price = $order->stop_loss;
            $orderLoss->value = $order->stop_loss * $order->quantity;
            $orderLoss->type = 'SELL';
            $orderLoss->condition = 'COND_LESS';
            $orderLoss->uuid = $order->uuid;
            $orderLoss->save();

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

    public function prepareActualPrices()
    {
        $marketSummaries = $this->api->getMarketSummaries();

        if (!$marketSummaries['success']) {
            return false;
        }

        $this->marketLastBids = BittrexParser::getPricesFromSummaries($marketSummaries);
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
}