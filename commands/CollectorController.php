<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Ticker;
use app\models\Api\Binance;
use app\utils\BinanceParser;

class CollectorController extends Controller
{
    public $marketsToCollect = [
        'BTC-NEO',
        'BTC-NAV',
        'BTC-LSK',
        'BTC-XRP',
        'BTC-ADA',
        'BTC-REP',
        'BTC-TRX'
    ];
    public function actionCollectTicker()
    {
        $api = new Binance();
        $tickerSource = $api->getTicker24();
        $slicedTickers = BinanceParser::sliceTicker($tickerSource, $this->marketsToCollect);
        foreach ($slicedTickers as $ticker) {
            $tickerData = BinanceParser::parseTicker($ticker);
            $ticker = new Ticker();
            $ticker->setAttributes($tickerData);
            $ticker->save();
        }
    }

    public function actionCollectOhlcv()
    {
        $api = new Binance();
    }
}
