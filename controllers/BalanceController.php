<?php

namespace app\controllers;

use app\models\Api\Bittrex;
use yii\data\ArrayDataProvider;

class BalanceController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $api = new Bittrex();

        $balance = $api->getBalances()['result'];
        $prices = $api->getActualPrices();

        foreach ($balance as &$crypto) {
            if ($crypto['Currency'] != 'BTC' && isset($prices['BTC-' . $crypto['Currency']])) {
                $crypto['Value'] = $crypto['Balance'] * $prices['BTC-' . $crypto['Currency']];
            }
            if ($crypto['Currency'] == 'BTC') {
                $crypto['Value'] =  $crypto['Balance'];
            }
        }

        $balanceProvider = new ArrayDataProvider([
            'allModels' => $balance,
            'sort' => [
                'attributes' => ['Currency','Value'],
            ],
        ]);

        return $this->render('index', [
            'balanceProvider' => $balanceProvider
        ]);
    }

}
