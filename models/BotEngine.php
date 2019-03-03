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
        $this->prepareActualPrices();
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

                    $order = new Order();

                    $order->uuid = $result['result']['uuid'];
                    $order->market = $pendingOrder->market;
                    $order->quantity = $pendingOrder->quantity;
                    $order->price = $pendingOrder->price;
                    $order->value = $pendingOrder->price * $pendingOrder->quantity;
                    $order->type = $pendingOrder->type;
                    $order->stop_loss = $pendingOrder->stop_loss;
                    $order->start_earn = $pendingOrder->start_earn;
                    $order->status = Order::STATUS_OPEN;

                    $order->save();
                }

                break;
            case 'SELL':
                $bestOffer = $actualTicker['result']['Bid'];
                $result = $this->api->placeSellOrder($pendingOrder->market, $pendingOrder->quantity, $bestOffer);
                break;
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

    public function prepareActualPrices()
    {
        $marketSummaries = $this->api->getMarketSummaries();

        if (!$marketSummaries['success']) {
            return false;
        }

        $this->marketLastBids = BittrexParser::getPricesFromSummaries($marketSummaries);
    }
}