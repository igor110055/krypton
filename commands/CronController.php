<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\BotEngine;

class CronController extends Controller
{
    public function actionIndex($message = 'hello world balbla')
    {
        $engine = new BotEngine();

        $engine->testMail();
    }
}
