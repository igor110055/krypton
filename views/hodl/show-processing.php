<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\HodlPositionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hodl processing positions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hodl-position-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterPosition' => GridView::FILTER_POS_HEADER,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'options' => [
            'class' => 'table-responsive',
        ],
        'rowOptions' => function ($model) {
            if ($model->price_diff < 0) {
                return ['class' => 'text-danger'];
            } elseif($model->price_diff > 0) {
                return ['class' => 'text-success'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'filter' => false
            ],
            [
                'attribute' => 'buy_date',
                'filter' => false
            ],
            'market',
            [
                'attribute' => 'quantity',
                'filter' => false
            ],
            [
                'attribute' => 'buy_price',
                'filter' => false
            ],
            [
                'attribute' => 'sell_price',
                'label' => 'Current price',
                'filter' => false
            ],
            [
                'attribute' => 'buy_value',
                'filter' => false
            ],
            [
                'attribute' => 'sell_value',
                'label' => 'Current value',
                'filter' => false
            ],
            [
                'attribute' => 'val_diff',
                'value' => function ($model){
                    return round($model->val_diff, 4);
                },
                'filter' => false
            ],
            [
                'attribute' => 'pln_buy_value',
                'filter' => false
            ],
            [
                'attribute' => 'pln_value',
                'filter' => false
            ],
            [
                'attribute' => 'pln_diff_value',
                'filter' => false
            ],
            [
                'attribute' => 'price_diff',
                'filter' => false
            ],
//            'comment',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
