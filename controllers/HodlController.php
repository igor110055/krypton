<?php

namespace app\controllers;

use app\models\Api\CoinGecko;
use app\models\BotEngine;
use app\utils\Currency;
use Yii;
use app\models\HodlPosition;
use app\models\HodlPositionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HodlController implements the CRUD actions for HodlPosition model.
 */
class HodlController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all HodlPosition models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HodlPositionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['like', 'status', HodlPosition::STATUS_DONE]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists processing HodlPosition models.
     * @return mixed
     */
    public function actionShowProcessing()
    {
        $botEngine = new BotEngine();
        $botEngine->prepareCurrentPrices();
        $currentPrices = $botEngine->getMarketLastBids();

        $params = Yii::$app->request->queryParams;

        $searchModel = new HodlPositionSearch();
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andFilterWhere(['like', 'status', HodlPosition::STATUS_PROCESSING]);

        $usdPrice = Currency::getUsdToPlnRate();

        $orders = (array)$dataProvider->getModels();
        $summary = [
            'quantity' => 0,
            'buy_value' => 0,
            'sell_value' => 0,
            'val_diff' => 0,
            'pln_buy_value' => 0,
            'pln_value' => 0,
            'pln_diff_value' => 0,
            'avg_price' => 0
        ];
        foreach ($orders as $order) {

            if (isset($currentPrices['Binance'][$order['market']])) {
                $currentPrice = $currentPrices['Binance'][$order['market']];
            } else {
                $currentPrice = $this->getPriceFromCoinGecko($order['market']);
            }

            $order['sell_price'] = $currentPrice;
            $diff = $order['sell_price'] - $order['buy_price'];
            $order['price_diff'] = $diff / $order['buy_price'] * 100;
            $order['sell_value'] = $order['quantity'] * $order['sell_price'];
            $order['val_diff'] = $order['sell_value'] - $order['buy_value'];
            $order['pln_buy_value'] = $order['buy_value'] * $usdPrice;
            $order['pln_value'] = $order['sell_value'] * $usdPrice;
            $order['pln_diff_value'] = ($order['sell_value'] * $usdPrice) - ($order['buy_value'] * $usdPrice);

            if (isset($params['HodlPositionSearch']) && $params['HodlPositionSearch']['market'] != '') {
                $summary['quantity'] += $order['quantity'];
            }
            $summary['buy_value'] += $order['buy_value'];
            $summary['sell_value'] += $order['sell_value'];
            $summary['val_diff'] += $order['val_diff'];
            $summary['pln_buy_value'] += $order['pln_buy_value'];
            $summary['pln_value'] += $order['pln_value'];
            $summary['pln_diff_value'] += $order['pln_diff_value'];
        }
        if (isset($params['HodlPositionSearch']) && $params['HodlPositionSearch']['market'] != '') {
            $summary['avg_price'] = $summary['buy_value'] / $summary['quantity'];
        }

        $globalDiff = $summary['sell_value'] - $summary['buy_value'];
        $summary['global_price_diff'] = $globalDiff / $summary['buy_value'] * 100;


        if (isset($params['sort']) && strstr($params['sort'],'price_diff')) {
            if(!strstr($params['sort'], '-')) {
                usort($orders, function($a, $b)
                {
                    return $a->price_diff < $b->price_diff;
                });
            } else {
                usort($orders, function($a, $b)
                {
                    return $b->price_diff < $a->price_diff;
                });
            }
        }

        $dataProvider->setModels($orders);

        return $this->render('show-processing', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'summary' => $summary
        ]);
    }


    /**
     * Displays a single HodlPosition model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new HodlPosition model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HodlPosition();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HodlPosition model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HodlPosition model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the HodlPosition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HodlPosition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HodlPosition::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //todo move this to external service
    private function getPriceFromCoinGecko($market)
    {
        $price = 0;
        $client = new CoinGecko();
        if ($market == 'YLDUSDT') {
            $ids = ['yield-app'];
            $result = $client->getTokenPrices($ids);
            $price = $result['yield-app']['usd'];
        }

        return $price;
    }
}
