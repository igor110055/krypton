<?php

use yii\helpers\Html;
use yii\grid\GridView;

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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
    ]); ?>
</div>
