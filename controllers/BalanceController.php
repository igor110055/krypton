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

        $summary = [];
        $sumValue = 0;

        foreach ($balance as $crypto) {

            if ($crypto['Currency'] != 'BTC' && isset($prices['BTC-' . $crypto['Currency']])) {
                $value = $crypto['Balance'] * $prices['BTC-' . $crypto['Currency']];
                if ($value > 0.0001) {
                    $summary[$crypto['Currency']]['Currency'] = $crypto['Currency'];
                    $summary[$crypto['Currency']]['Balance'] = $crypto['Balance'];
                    $summary[$crypto['Currency']]['Price'] = $prices['BTC-' . $crypto['Currency']];
                    $summary[$crypto['Currency']]['Value'] = $value;
                    $sumValue += $value;
                }
            }
            if ($crypto['Currency'] == 'BTC') {
                $summary['BTC']['Currency'] = 'BTC';
                $summary['BTC']['Balance'] = $crypto['Balance'];
                $summary['BTC']['Price'] = 0;
                $summary['BTC']['Value'] = $crypto['Balance'];
                $sumValue += $crypto['Balance'];
            }
        }

        $balanceProvider = new ArrayDataProvider([
            'allModels' => $summary,
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        return $this->render('index', [
            'balanceProvider' => $balanceProvider,
            'sumValue' => $sumValue
        ]);
    }

}
