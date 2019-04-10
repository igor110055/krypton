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
    public function actionCollectTicker()
    {
        $api = new Binance();
        $tickerSource = $api->getTicker24('NAVBTC');
        $tickerData = BinanceParser::parseTicker($tickerSource);

        $ticker = new Ticker();
        $ticker->setAttributes($tickerData);
        $ticker->save();
    }

    public function actionCollectOhlcv()
    {
        $api = new Binance();
    }
}
