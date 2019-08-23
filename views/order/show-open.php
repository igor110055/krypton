<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Open Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-show-open">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('orderSoldResult')): ?>

        <div class="alert alert-success">
            <?php echo Yii::$app->session->getFlash('orderSoldResult'); ?>
        </div>

    <?php endif; ?>

    <?php
    Pjax::begin(['id' => 'processing_orders_grid']);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'rowOptions' => function ($model) {
            if ($model->price_diff < 0) {
//                    return ['class' => 'bg-danger'];
                return ['class' => 'text-danger'];
            } elseif($model->price_diff > 0) {
//                    return ['class' => 'bg-success'];
                return ['class' => 'text-success'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            'uuid',
            'type',
            'market',
            'quantity',
            'quantity_remaining',
            'price',
            'current_price',
//            'value',
//            'current_value',
            'price_diff',
//            'stop_loss',
//            'take_profit',
            'status',
            'crdate',
            'open_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete} {sell}',
                'buttons' => [
                    'sell' => function ($url, $model, $key) {
                        return Html::a('Sell', $url);
                    }
                ]
            ],
        ],
    ]);
    Pjax::end();

    Pjax::begin(['id' => 'diff_orders_grid']);
    echo GridView::widget([
        'dataProvider' => $diffProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'OrderUuid',
            'Exchange',
            'OrderType',
            'Quantity',
            'QuantityRemaining',
            'Limit',
            'CommissionPaid',
            'Price',
            'PricePerUnit',
            'Opened',
        ],

    ]);
    Pjax::end();

    ?>

</div>
