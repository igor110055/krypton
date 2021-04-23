<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\PortfolioEngine;
use yii\console\Controller;
use app\models\BotEngine;
use app\models\EndPointCacher;
use app\models\Api\Bittrex;
use app\models\Configuration;

class CronController extends Controller
{
    public function actionMinute(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $configuration = new Configuration();
            $checkPendingOrders = (int)$configuration->getValue('check_pending_orders');
            $engine = new BotEngine();
            $engine->prepareCurrentPrices();
            $engine->checkAlerts();
            if ($checkPendingOrders) {
                $engine->checkPendingOrders();
            }
            sleep(3);
            $engine->checkOpenOrders();
            $engine->createPendingOrdersForClosedOrders();
            sleep(15);
        }
    }

    public function actionCheckOpenOrders2()
    {
        $engine = new BotEngine();
        $engine->checkOpenOrders2();
    }

    public function actionCheckBalances()
    {
        $engine = new BotEngine();
        $engine->prepareCurrentPrices();
        $engine->checkBalancesForOrders();
    }

    public function actionDaily(): void
    {
        $portfolioEngine = new PortfolioEngine();
        $portfolioEngine->handleTickerMonitor();
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
