<?php
namespace app\controllers;

use app\interfaces\ExchangeInterface;
use app\models\Api\Binance;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class AjaxController extends Controller
{
    public function actionGetTicker() {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->get();
            $api = Yii::createObject(['class' => 'app\models\Api\\' . $data['exchange']]);

            /* @var ExchangeInterface $api */
            $makretData = $api->getTickerFormatted($data['market']);

            return $makretData;
        }
    }

    public function actionGetMarkets()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->get();

            /* @var ExchangeInterface $api */
            $api = Yii::createObject(['class' => 'app\models\Api\\' . $data['exchange']]);

            return $api->getMarketsFormatted();
        }
    }

    public function actionGetPrecision()
    {
        if (Yii::$app->request->isAjax) {
            $step = 1;

            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->get();

            $api = new Binance();

            $exchangeInfo = $api->getExchangeInfo();

            foreach ($exchangeInfo['symbols'] as $symbol) {
                if ($symbol['symbol'] == $data['market']) {
                    $step = strpos($symbol['filters'][2]['stepSize'], '1', 0) - 1;
                }
            }
            return $step;
        }
    }
}