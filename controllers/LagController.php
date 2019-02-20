<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Api\Bittrex;
use app\models\Api\Binance;
use app\utils\BittrexParser;
use app\utils\BinanceParser;
use yii\data\ArrayDataProvider;

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
        foreach ($bittrexPrices as $market => $value) {
            if (isset($binancePrices[$market])) {
                $diff = floatval($binancePrices[$market]) - floatval($value);
                $comparedPrices[$market] = [
                    'Market' => $market,
                    'Bittrex' => number_format($value, 8),
                    'Binance' => number_format($binancePrices[$market], 8),
                    'Diff' => number_format($diff, 8),
                ];
            }
        }

        unset($comparedPrices['BTC-USDS']);

        $provider = new ArrayDataProvider([
            'allModels' => $comparedPrices,
            'pagination' => [
                'pageSize' => 200,
            ],
            'sort' => [
                'attributes' => ['Market','Diff'],
            ],
        ]);

        return $this->render('index', [
            'provider' => $provider
        ]);
    }
}