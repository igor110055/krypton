<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders history';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'rowOptions' => function ($model) {
            if ($model->sell_price > 0) {
                $diff = $model->sell_price - $model->price;
                if ($diff < 0) {
//                    return ['class' => 'bg-danger'];
                    return ['class' => 'text-danger'];
                } else {
//                    return ['class' => 'bg-success'];
                    return ['class' => 'text-success'];
                }
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'market',
            'quantity',
            ['attribute' => 'price',
                'value' => function($model) {
                    if ($model->price > 0) {
                        $val = number_format($model->price, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            ['attribute' => 'value',
                'value' => function($model) {
                    if ($model->value > 0) {
                        $val = number_format($model->value, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            ['attribute' => 'sell_price',
                'value' => function($model) {
                    if ($model->sell_price > 0) {
                        $val = number_format($model->sell_price, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            ['attribute' => 'sell_value',
                'value' => function($model) {
                    if ($model->sell_value > 0) {
                        $val = number_format($model->sell_value, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            ['attribute' => 'val_diff',
                'value' => function($model) {
                    if ($model->sell_value > 0) {
                        $val = number_format($model->sell_value - $model->value, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            ['attribute' => 'price_diff',
                'value' => function($model) {
                    if ($model->sell_price > 0) {
                        $diff = $model->sell_price - $model->price;
                        $val = round($diff / $model->sell_price * 100, 2);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            'status',
            'crdate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    Pjax::end();
    ?>

</div>
