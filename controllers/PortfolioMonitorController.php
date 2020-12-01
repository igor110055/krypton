<?php

namespace app\controllers;

use app\models\PortfolioTickerSearch;
use yii\web\Controller;

class PortfolioMonitorController extends Controller
{
    /**
     * Lists all Alert models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PortfolioTickerSearch();
        $dataProvider = $searchModel->searchAll();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
