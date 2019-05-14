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
        'BTC-ETH',
        'BTC-XRP',
        'BTC-LTC',
        'BTC-NEO',
        'BTC-XLM',
        'BTC-NAV',
        'BTC-LSK',
        'BTC-ADA',
        'BTC-REP',
        'BTC-TRX',
        'BTC-XMR',
        'BTC-DASH',
        'BTC-ONT',
        'BTC-XEM',
        'BTC-ZEC',
        'BTC-BAT',
        'BTC-WAVES',
        'BTC-OMG',
        'BTC-QTUM',
        'BTC-DCR',
        'BTC-ZIL',
        'BTC-BTS',
        'BTC-IOST',
        'BTC-XVG',
        'BTC-STEEM',
        'BTC-NPXS',
        'BTC-BTT',
        'BTC-SC',
        'BTC-ENJ',
        'BTC-KMD',
        'BTC-STRAT',
        'BTC-GNT',
        'BTC-ARK',
        'BTC-XZC',
        'BTC-PIVX'
    ];

    public function actionCollectTicker()
    {
        $api = new Binance();
        for ($i = 0; $i < 5; $i++) {
            $tickerSource = $api->getTicker24();
            $slicedTickers = BinanceParser::sliceTicker($tickerSource, $this->marketsToCollect);
            foreach ($slicedTickers as $ticker) {
                $tickerData = BinanceParser::parseTicker($ticker);
                $ticker = new Ticker();
                $ticker->setAttributes($tickerData);
                $ticker->save();
            }
            sleep(10);
        }
    }

    public function actionCollectOhlcv()
    {
        $api = new Binance();
    }

    public function actionCleanDb()
    {
        $weekAgo = strtotime("-1 week");
        $dateWeekAgo = date('Y-m-d', $weekAgo);

        Ticker::deleteAll('created_at < :dateWeekAgo', ['dateWeekAgo' => $dateWeekAgo]);
    }
}
