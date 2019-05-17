<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Ticker;
use app\models\Api\Binance;
use app\utils\BinanceParser;

class CollectorController extends Controller
{
    public function actionCollectTicker()
    {
        $marketsToCollect = Yii::$app->params['markets'];
        $api = new Binance();
        for ($i = 0; $i < 5; $i++) {
            $tickerSource = $api->getTicker24();
            $slicedTickers = BinanceParser::sliceTicker($tickerSource, $marketsToCollect);
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
        $weekAgo = strtotime("-3 days");
        $dateWeekAgo = date('Y-m-d', $weekAgo);

        Ticker::deleteAll('created_at < :dateWeekAgo', ['dateWeekAgo' => $dateWeekAgo]);
    }
}
