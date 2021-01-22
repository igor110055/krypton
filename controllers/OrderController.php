<?php

namespace app\controllers;

use app\models\Api\Bittrex;
use app\models\BotEngine;
use Yii;
use app\models\Order;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    private $currentPrices = [];
    private $botEngine;

    public function __construct($id, $module, $config = [])
    {
        $this->botEngine = new BotEngine();
        $this->botEngine->prepareCurrentPrices();
        $this->currentPrices = $this->botEngine->getMarketLastBids();

        parent::__construct($id, $module, $config);
    }
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Order::find()->where(['status' => Order::STATUS_DONE])
            ->orderBy(['sell_placed' => SORT_DESC, 'crdate' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionShowProcessing()
    {
        $query = Order::find()->where(['status' => Order::STATUS_PROCESSED])
            ->orderBy(['crdate' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $orders = (array)$dataProvider->getModels();
        foreach ($orders as $order) {
            $order['price'] = (float)$order['price'];
            $order['current_price'] = (float)$this->currentPrices[$order['exchange']][$order['market']];
            $diff = $order['current_price'] - $order['price'];
            $order['price_diff'] = round($diff / $order['price'] * 100, 2);
            $order['current_value'] = $order['quantity'] * $order['current_price'];
            $order['value_diff'] = $order['current_value'] - $order['value'];
        }
        $dataProvider->setModels($orders);

        return $this->render('show-processing', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionShowOpen()
    {
        $query = Order::find()->where(['status' => Order::STATUS_OPEN])
            ->orderBy(['crdate' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $api = new Bittrex();
        $openOrdersExchange = $api->getOpenOrders();
        $openOrdersExchangeData = [];
        $openOrdersExchangeUuids = [];
        foreach ($openOrdersExchange['result'] as $openOrder) {
            $openOrdersExchangeUuids[] = $openOrder['OrderUuid'];
            $openOrdersExchangeData[$openOrder['OrderUuid']] = $openOrder;
        }

        $orders = (array)$dataProvider->getModels();
        $ordersUuids = [];

        foreach ($orders as $order) {
            if (!isset($openOrdersExchangeData[$order['uuid']])) {
                continue;
            }
            $ordersUuids[] = $order['uuid'];
            $order['price'] = number_format($order['price'], 8);
            $order['current_price'] = number_format($this->currentPrices[$order['exchange']][$order['market']], 8);
            $diff = $order['current_price'] - $order['price'];
            $order['price_diff'] = round($diff / $order['price'] * 100, 2);
            $order['quantity_remaining'] = $openOrdersExchangeData[$order['uuid']]['QuantityRemaining'];
            $order['open_date'] = $openOrdersExchangeData[$order['uuid']]['Opened'];
        }
        $dataProvider->setModels($orders);

        $exchangeDiff = array_diff($openOrdersExchangeUuids, $ordersUuids);
        $diffToShow = [];
        foreach ($exchangeDiff as $diffUuid) {
            $diffToShow[] = $openOrdersExchangeData[$diffUuid];
        }

        $diffProvider = new ArrayDataProvider([
                'allModels' => $diffToShow,
                'key' => function ($model) {
                    return $model['OrderUuid'];
                }
            ]
        );

        return $this->render('show-open', [
            'dataProvider' => $dataProvider,
            'diffProvider' => $diffProvider
        ]);
    }
    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->price = number_format($model->price, 8);
        if ($model->stop_loss > 0) {
            $model->stop_loss = number_format($model->stop_loss, 8);
        }
        if ($model->take_profit > 0) {
            $model->take_profit = number_format($model->take_profit, 8);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
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

    public function actionSell($id)
    {
        $order = $this->findModel($id);
        if (!$order) {
            return false;
        }

        $bot = new BotEngine();
        $sellResult = $bot->sellOrder($order);
        if ($sellResult['success']) {
            Yii::$app->session->setFlash('orderSoldResult', 'Order sold successfull');
        } else {
            Yii::$app->session->setFlash('orderSoldResult', 'Error:' . $sellResult['msg']);
        }

        return $this->redirect(['show-processing']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
