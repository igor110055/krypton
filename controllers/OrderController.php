<?php

namespace app\controllers;

use app\models\Api\Bittrex;
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
        $orders = Order::find()->orderBy(['crdate' => SORT_DESC])->asArray()->all();

        $api = new Bittrex();
        $currentPrices = $api->getActualPrices();
        $newOrders = [];
        $processingOrders = [];
        $historyOrders = [];

        foreach ($orders as $order) {
            $order['price'] = number_format($order['price'], 8);
            $order['current_price'] = number_format($currentPrices[$order['market']], 8);
            $diff = $order['current_price'] - $order['price'];
            $order['price_diff'] = round($diff / $order['current_price'] * 100, 2);
            $order['current_value'] = number_format($order['quantity'] * $order['current_price'], 8);
            if ($order['stop_loss'] > 0) {
                $order['stop_loss'] = number_format($order['stop_loss'], 8);
            }
            if ($order['take_profit'] > 0) {
                $order['take_profit'] = number_format($order['take_profit'], 8);
            }
            switch ($order['status']) {
                case Order::STATUS_CLOSED:
                case Order::STATUS_OPEN:
                    $newOrders[] = $order;
                    break;
                case Order::STATUS_PROCESSED:
                    $processingOrders[] = $order;
                    break;
                default:
                    $historyOrders[] = $order;
                    break;
            }
        }

        $newOrdersProvider = new ArrayDataProvider([
            'allModels' => $newOrders,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        $processingOrdersProvider = new ArrayDataProvider([
            'allModels' => $processingOrders,
            'pagination' => [
                'pageSize' => 50,
            ],

        ]);
        $historyOrdersProvider = new ArrayDataProvider([
            'allModels' => $historyOrders,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'newOrdersProvider' => $newOrdersProvider,
            'processingOrdersProvider' => $processingOrdersProvider,
            'historyOrdersProvider' => $historyOrdersProvider,

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
