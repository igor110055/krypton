<?php

use app\models\PortfolioTickerSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Monitor';

/* @var $this yii\web\View */
/* @var $searchModel PortfolioTickerSearch */
/* @var $dataProvider ActiveDataProvider */
?>
<div class="alert-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'created_at',
            'hodl_btc_value',
            'hodl_percent',
            'exchange_btc_value',
            'btc_value',
            'btc_price',
            'usdt_value',
            'usd_price',
            'pln_value',
            'deposit',
            'pln_diff',
            'change',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
