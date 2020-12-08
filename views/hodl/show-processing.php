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

            'id',
            'buy_date',
            'market',
            'quantity',
            'buy_price',
            [
                'attribute' => 'sell_price',
                'label' => 'Current price'
            ],
            'buy_value',
            [
                'attribute' => 'sell_value',
                'label' => 'Current value'
            ],
            [
                'attribute' => 'val_diff',
                'value' => function ($model){
                    return round($model->val_diff, 4);
                }
            ],
            'price_diff',
            'pln_value',
            'pln_diff_value',
            'comment',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
