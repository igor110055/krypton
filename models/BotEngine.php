<?php
namespace app\models;

use app\utils\BittrexParser;
use Yii;
use app\models\Alert;
use app\models\Api\Bittrex;

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