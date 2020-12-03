<?php

namespace app\controllers;

use app\models\Api\Binance;
use app\models\Api\Bittrex;
use app\models\BotEngine;
use app\models\Configuration;
use app\models\HodlPosition;
use app\utils\BinanceParser;
use app\utils\BittrexParser;
use app\utils\Currency;
use yii\data\ArrayDataProvider;

class BalanceController extends \yii\web\Controller
{
    private $configuration;
    private $currentPrices = [];
    private $botEngine;
    private $Bittrex;
    private $Binance;

    public function __construct($id, $module, $config = [])
    {
        $this->configuration = new Configuration();
        $this->Bittrex = new Bittrex();
        $this->Binance = new Binance();
        $this->botEngine = new BotEngine();
        $this->botEngine->prepareCurrentPrices();
        $this->currentPrices = $this->botEngine->getMarketLastBids();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $hodlBTCvalueSum = HodlPosition::getProcessingBTCvalueSum($this->currentPrices);

        $bittrexBalance = $this->Bittrex->getBalances()['result'];
        $bittrexSummary = BittrexParser::getSummary($bittrexBalance, $this->currentPrices['Bittrex']);

        $binanceBalance = $this->Binance->getAccountInfo();
        $binanceSummary = BinanceParser::getSummary($binanceBalance, $this->currentPrices['Binance']);

        $bittrexBalanceProvider = new ArrayDataProvider([
            'allModels' => $bittrexSummary['summary'],
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        $binanceBalanceProvider = new ArrayDataProvider([
            'allModels' => $binanceSummary['summary'],
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        $btcPrice = $this->Bittrex->getBtcUsdPrice();
        $plnPrice = Currency::getUsdToPlnRate();

        return $this->render('index', [
            'bittrexBalanceProvider' => $bittrexBalanceProvider,
            'binanceBalanceProvider' => $binanceBalanceProvider,
            'bittrexSumValue' => $bittrexSummary['sumBTC'],
            'binanceSumValue' => $binanceSummary['sumBTC'],
            'binanceSumValueUSDT' => $binanceSummary['sumUSDT'],
            'btcPrice' => $btcPrice['result'],
            'plnPrice' => $plnPrice,
            'configuration' => $this->configuration,
            'hodlBTCvalueSum' => $hodlBTCvalueSum
        ]);
    }
}
