<?php

namespace app\controllers;

use app\models\BotEngine;
use Yii;
use app\models\HodlPosition;
use app\models\HodlPositionSearch;
use yii\data\ActiveDataProvider;
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

        $query = HodlPosition::find()->where(['status' => HodlPosition::STATUS_PROCESSING])
            ->orderBy(['buy_date' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $orders = (array)$dataProvider->getModels();
        foreach ($orders as $order) {
            $order['sell_price'] = $currentPrices['Binance'][$order['market']];
            $diff = $order['sell_price'] - $order['buy_price'];
            $order['price_diff'] = round($diff / $order['sell_price'] * 100, 2);
            $order['sell_value'] = $order['quantity'] * $order['sell_price'];
            $order['val_diff'] = $order['sell_value'] - $order['buy_value'];
        }
        $dataProvider->setModels($orders);

        return $this->render('show-processing', [
            'dataProvider' => $dataProvider,
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
            return $this->redirect(['view', 'id' => $model->id]);
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
}
