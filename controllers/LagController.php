<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Api\Bittrex;
use app\models\Api\Binance;
use app\utils\BittrexParser;
use app\utils\BinanceParser;


class LagController extends Controller
{

    public function actionIndex()
    {
        $bittrexApi = new Bittrex();
        $binanceApi = new Binance();

        $bittrexSummaries = $bittrexApi->getMarketSummaries();
        $binanceSummaries = $binanceApi->getPrices();

        $bittrexPrices = BittrexParser::getPricesFromSummaries($bittrexSummaries);
        $binancePrices = BinanceParser::parsePrices($binanceSummaries);

        $comparedPrices = [];

        $result = '';

        return $this->render('index', [
            'result' => $binancePrices
        ]);
    }
}