<?php
namespace app\models;

use Yii;
use app\models\Alert;
use app\models\Api\Bittrex;

class BotEngine
{
    public function testMail()
    {
        $mail = Yii::$app->mailer->compose();
        $mail->setFrom('admin@wales.usermd.net')
            ->setTo('leszek.walszewski@gmail.com')
            ->setSubject('Message subject'.time())
            ->setTextBody('Dodano zlecenie przez crona crona'.time())
            ->send();
    }

    public function checkAlerts()
    {
        $alerts = Alert::find()->all();

        $api = new Bittrex();
        $marketSummaries = $api->getMarketSummaries();

        if (!$marketSummaries['success']) {
            return false;
        }

        foreach ($marketSummaries['result'] as $marketSummary) {
            if (strstr($marketSummary['MarketName'], 'BTC')){
                $marketLastBids[$marketSummary['MarketName']] = $marketSummary['Last'];
            }
        }

        foreach ($alerts as $alert) {
            $alertData = $alert->getAttributes();
            switch ($alertData['condition']) {
                case 'COND_MORE': '';
            }
        }



        //pętla po alertach
        //mail jeśli warunek spełniony
    }
}