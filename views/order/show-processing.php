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

    <?php if (Yii::$app->session->hasFlash('orderSoldResult')): ?>

        <div class="alert alert-success">
            <?php echo Yii::$app->session->getFlash('orderSoldResult'); ?>
        </div>

    <?php endif; ?>

    <?php
    echo GridView::widget([
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
//            'uuid',
//            'type',
            'exchange',
            'market',
            'quantity',
            [
                'attribute' => 'price',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->price, 8, '.', '');
                    } else {
                        return number_format($model->price, 4, '.', '');
                    }
                }
            ],
            [
                'attribute' => 'current_price',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->current_price, 8, '.', '');
                    } else {
                        return number_format($model->current_price, 4, '.', '');
                    }
                }
            ],
            'value',
            [
                'attribute' => 'current_value',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->current_value, 8, '.', '');
                    } else {
                        return number_format($model->current_value, 4, '.', '');
                    }
                }
            ],
            'price_diff',
            [
                'attribute' => 'stop_loss',
                'value' => function ($model){
                    if ($model->stop_loss > 0) {
                        if (strstr($model->market, 'BTC')) {
                            return number_format($model->stop_loss, 8, '.', '');
                        } else {
                            return number_format($model->stop_loss, 4, '.', '');
                        }
                    }
                }
            ],
            [
                'attribute' => 'take_profit',
                'value' => function ($model){
                    if ($model->take_profit > 0) {
                        if (strstr($model->market, 'BTC')) {
                            return number_format($model->take_profit, 8, '.', '');
                        } else {
                            return number_format($model->take_profit, 4, '.', '');
                        }
                    }
                }
            ],
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

    ?>

</div>
