<?php

namespace app\controllers;

use app\models\Api\Binance;
use app\models\Api\Bittrex;
use app\models\BotEngine;
use yii\data\ArrayDataProvider;

class BalanceController extends \yii\web\Controller
{
    private $currentPrices = [];
    private $botEngine;
    private $Bittrex;
    private $Binance;

    public function __construct($id, $module, $config = [])
    {
        $this->Bittrex = new Bittrex();
        $this->Binance = new Binance();
        $this->botEngine = new BotEngine();
        $this->botEngine->prepareCurrentPrices();
        $this->currentPrices = $this->botEngine->getMarketLastBids();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $bittrexBalance = $this->Bittrex->getBalances()['result'];
        $bittrexSummary = $this->getBittrexSummary($bittrexBalance);

        $binanceBalance = $this->Binance->getAccountInfo();
        $binanceSummary = [];
        $binanceSum = 0;

        foreach ($binanceBalance['balances'] as $asset) {
            if (($asset['free'] + $asset['locked'] > 0) && $asset['asset'] != 'BTC' && $asset['asset'] != 'USDT' && $asset['asset'] != 'BUSD') {
                $binanceSummary[$asset['asset']]['Currency'] = $asset['asset'];
                $binanceSummary[$asset['asset']]['Balance'] = $asset['free'] + $asset['locked'];
                $binanceSummary[$asset['asset']]['Price'] = $this->currentPrices['Binance'][$asset['asset'] . 'BTC'];
                $binanceSummary[$asset['asset']]['Value'] = number_format($binanceSummary[$asset['asset']]['Balance'] * $binanceSummary[$asset['asset']]['Price'], 8);
                $binanceSum += $binanceSummary[$asset['asset']]['Value'];
            }
            if ($asset['asset'] == 'BTC') {
                $binanceSummary['BTC']['Currency'] ='BTC';
                $binanceSummary['BTC']['Balance'] = $asset['free'] + $asset['locked'];
                $binanceSummary['BTC']['Price'] = 0;
                $binanceSummary['BTC']['Value'] = $binanceSummary['BTC']['Balance'];
                $binanceSum += $binanceSummary[$asset['asset']]['Value'];
            }
            if ($asset['asset'] == 'USDT') {
                $binanceSummary['USDT']['Currency'] ='USDT';
                $binanceSummary['USDT']['Balance'] = $asset['free'] + $asset['locked'];
                $binanceSummary['USDT']['Price'] = $this->currentPrices['Bittrex']['BTC-TUSD'];;
                $binanceSummary['USDT']['Value'] = number_format($binanceSummary[$asset['asset']]['Balance'] * $binanceSummary[$asset['asset']]['Price'], 8);;
                $binanceSum += $binanceSummary[$asset['asset']]['Value'];
            }
            if ($asset['asset'] == 'BUSD') {
                $binanceSummary['BUSD']['Currency'] ='BCHA';
                $binanceSummary['BUSD']['Balance'] = $asset['free'] + $asset['locked'];
                $binanceSummary['BUSD']['Price'] = $this->currentPrices['Bittrex']['BTC-TUSD'];;
                $binanceSummary['BUSD']['Value'] = number_format($binanceSummary[$asset['asset']]['Balance'] * $binanceSummary[$asset['asset']]['Price'], 8);;
                $binanceSum += $binanceSummary[$asset['asset']]['Value'];
            }
        }


        $bittrexBalanceProvider = new ArrayDataProvider([
            'allModels' => $bittrexSummary['bittrexSummary'],
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        $binanceBalanceProvider = new ArrayDataProvider([
            'allModels' => $binanceSummary,
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        return $this->render('index', [
            'bittrexBalanceProvider' => $bittrexBalanceProvider,
            'binanceBalanceProvider' => $binanceBalanceProvider,
            'bittrexSumValue' => $bittrexSummary['bittrexSumValue'],
            'binanceSum' => $binanceSum
        ]);
    }

    private function getBittrexSummary(array $bittrexBalance): array
    {
        $bittrexSummary = [];
        $bittrexSumValue = 0;

        foreach ($bittrexBalance as $crypto) {

            if ($crypto['Currency'] != 'BTC' && isset($this->currentPrices['Bittrex']['BTC-' . $crypto['Currency']])) {
                $value = $crypto['Balance'] * $this->currentPrices['Bittrex']['BTC-' . $crypto['Currency']];
                if ($value > 0.0001) {
                    $bittrexSummary[$crypto['Currency']]['Currency'] = $crypto['Currency'];
                    $bittrexSummary[$crypto['Currency']]['Balance'] = $crypto['Balance'];
                    $bittrexSummary[$crypto['Currency']]['Price'] = $this->currentPrices['Bittrex']['BTC-' . $crypto['Currency']];
                    $bittrexSummary[$crypto['Currency']]['Value'] = $value;
                    $bittrexSumValue += $value;
                }
            }
            if ($crypto['Currency'] == 'BTC') {
                $bittrexSummary['BTC']['Currency'] = 'BTC';
                $bittrexSummary['BTC']['Balance'] = $crypto['Balance'];
                $bittrexSummary['BTC']['Price'] = 0;
                $bittrexSummary['BTC']['Value'] = $crypto['Balance'];
                $bittrexSumValue += $crypto['Balance'];
            }
        }

        return [
            'bittrexSummary' => $bittrexSummary,
            'bittrexSumValue' => $bittrexSumValue
        ];
    }


}
