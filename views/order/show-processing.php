<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Processing Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-show-processing">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    Pjax::begin(['id' => 'processing_orders_grid']);
    echo GridView::widget([
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

    ?>

</div>
