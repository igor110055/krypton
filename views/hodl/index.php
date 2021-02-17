<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\HodlPositionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hodl Positions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hodl-position-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Hodl Position', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'buy_date',
            'sell_date',
            'market',
            'quantity',
            'buy_price',
            'sell_price',
            'buy_value',
            'sell_value',
//            'status',
            'val_diff',
            'price_diff',
            'pln_value',
            'pln_diff_value',
            'comment',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
