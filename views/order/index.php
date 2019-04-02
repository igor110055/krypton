<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <h2>Processing Orders</h2>
    <?php
    Pjax::begin(['id' => 'processing_orders_grid']);
    echo GridView::widget([
        'id' => 'processing_orders_grid',
        'dataProvider' => $processingOrdersProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'uuid',
            'type',
            'market',
            'quantity',
            'price',
            'current_price',
            'value',
            'current_value',
            'price_diff',
            'stop_loss',
            'take_profit',
            'status',
            'crdate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    Pjax::end();

    ?>

    <h2>History Orders</h2>
    <?php
    Pjax::begin(['id' => 'history_orders_grid']);
    echo GridView::widget([
        'dataProvider' => $historyOrdersProvider,
        'id' => 'history_orders_grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'uuid',
            'type',
            'market',
            'quantity',
            'price',
            'current_price',
            'value',
            'current_value',
            'price_diff',
            'stop_loss',
            'take_profit',
            'status',
            'crdate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    Pjax::end();
    ?>

    <h2>New Orders</h2>
    <?php
    Pjax::begin(['id' => 'new_orders_grid']);
    echo GridView::widget([
        'id' => 'new_orders_grid',
        'dataProvider' => $newOrdersProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'uuid',
            'type',
            'market',
            'quantity',
            'price',
            'current_price',
            'value',
            'current_value',
            'price_diff',
            'stop_loss',
            'take_profit',
            'status',
            'crdate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    Pjax::end();
    ?>


</div>
