<?php

namespace app\controllers;

use app\models\Api\Binance;
use app\models\Api\Bittrex;
use app\models\BotEngine;
use app\models\Configuration;
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
        $bittrexBalance = $this->Bittrex->getBalances()['result'];
        $bittrexSummary = BittrexParser::getBittrexSummary($bittrexBalance, $this->currentPrices);

        $binanceBalance = $this->Binance->getAccountInfo();
        $binanceSummary = BinanceParser::getBinanceSummary($binanceBalance, $this->currentPrices);

        $bittrexBalanceProvider = new ArrayDataProvider([
            'allModels' => $bittrexSummary['bittrexSummary'],
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        $binanceBalanceProvider = new ArrayDataProvider([
            'allModels' => $binanceSummary['binanceSummary'],
            'sort' => [
                'attributes' => ['Value', 'Currency'],
            ],
        ]);

        $btcPrice = $this->Bittrex->getBtcUsdPrice();
        $plnPrice = Currency::getUsdToPlnRate();

        return $this->render('index', [
            'bittrexBalanceProvider' => $bittrexBalanceProvider,
            'binanceBalanceProvider' => $binanceBalanceProvider,
            'bittrexSumValue' => $bittrexSummary['bittrexSumValue'],
            'binanceSumValue' => $binanceSummary['binanceSumValue'],
            'btcPrice' => $btcPrice['result'],
            'plnPrice' => $plnPrice,
            'configuration' => $this->configuration,
        ]);
    }
}
