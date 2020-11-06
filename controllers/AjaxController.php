<?php
namespace app\controllers;

use app\interfaces\ExchangeInterface;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Api\Bittrex;
use app\models\Api\Binance;

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
}