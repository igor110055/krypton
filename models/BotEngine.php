<?php
namespace app\models;

use Yii;
use app\models\Alert;
use app\models\Api\Bittrex;

class BotEngine
{
    public function checkAlerts()
    {
        $api = new Bittrex();
        $marketSummaries = $api->getMarketSummaries();

        if (!$marketSummaries['success']) {
            return false;
        }

        $alerts = Alert::findAll([
            'is_active' => 1
        ]);

        foreach ($marketSummaries['result'] as $marketSummary) {
            if (strstr($marketSummary['MarketName'], 'BTC')){
                $marketLastBids[$marketSummary['MarketName']] = $marketSummary['Last'];
            }
        }

        foreach ($alerts as $alert) {

            $alertData = $alert->getAttributes();
            $actualMarketPrice = $marketLastBids[$alertData['market']];

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
}