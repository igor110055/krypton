<?php
namespace app\controllers;

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

            $api = new Bittrex();
            $makretData = $api->getTicker($data['market']);

            $res = $makretData;

            return $res;
        }
    }

    public function actionGetMarkets()
    {
//        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = Yii::$app->request->get();
            /*
             * var ExchangeInterface
             */
            $api = Yii::createObject(['class' => 'app\models\Api\\' . $data['exchange']]);

            return $api->getMarketsFormatted();

//        }
    }
}