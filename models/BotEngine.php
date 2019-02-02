<?php
namespace app\models;

use Yii;

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
}