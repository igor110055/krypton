<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\BotEngine;
use app\models\EndPointCacher;
use app\models\Api\Bittrex;
use app\models\Configuration;

class CronController extends Controller
{
    public function actionMinute()
    {
        $configuration = new Configuration();
        $checkPendingOrders = (int)$configuration->getValue('check_pending_orders');
        $engine = new BotEngine();
        $engine->prepareCurrentPrices();
        $engine->checkAlerts();
        if ($checkPendingOrders) {
            $engine->checkPendingOrders();
        }
        $engine->checkOpenOrders();
        $engine->createPendingOrdersForClosedOrders();
    }

    public function actionDownloadMarkets()
    {
        $bittrexApi = new Bittrex();
        $bittrexCacher = new EndPointCacher($bittrexApi);

        $bittrexCacher->downloadMarkets();

    }

    public function actionCheckAlerts()
    {
        $engine = new BotEngine();

        $engine->checkAlerts();
    }
}
