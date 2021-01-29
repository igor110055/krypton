<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\OrderSearch */
/* @var $summary array */

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
        'filterModel' => $searchModel,
        'filterPosition' => GridView::FILTER_POS_HEADER,
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
        'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'filter' => false
            ],
            [
                'attribute' => 'exchange',
                'filter' => ['binance' => 'Binance', 'bittrex' => 'Bittrex'],
            ],
            [
                'attribute' => 'market',
                'filter' => ['btc' => 'BTC', 'usdt' => 'USDT'],
            ],
            [
                'attribute' => 'quantity',
                'filter' => false,
            ],
            [
                'attribute' => 'price',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->price, 8, '.', '');
                    } else {
                        return number_format($model->price, 4, '.', '');
                    }
                },
                'filter' => false,
            ],
            [
                'attribute' => 'current_price',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->current_price, 8, '.', '');
                    } else {
                        return number_format($model->current_price, 4, '.', '');
                    }
                },
                'filter' => false,
            ],
            [
                'attribute' => 'value',
                'filter' => false,
                'footer' => round($summary['value'], 8)
            ],
            [
                'attribute' => 'current_value',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->current_value, 8, '.', '');
                    } else {
                        return number_format($model->current_value, 4, '.', '');
                    }
                },
                'footer' => round($summary['current_value'], 8)
            ],
            [
                'attribute' => 'value_diff',
                'value' => function ($model){
                    if (strstr($model->market, 'BTC')) {
                        return number_format($model->value_diff, 8, '.', '');
                    } else {
                        return number_format($model->value_diff, 4, '.', '');
                    }
                },
                'footer' => round($summary['value_diff'], 8)
            ],
            [
                'attribute' => 'price_diff',
                'label' => 'Price %',
                'footer' => round($summary['global_price_diff'], 2)
            ],
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
                },
                'filter' => false,
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
                },
                'filter' => false,
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
<table>
    <tr>
        <td style="width: 100px">Value</td>
        <td><?php echo round($summary['value_USDT'], 2); ?></td>
    </tr>
    <tr>
        <td>Current value</td>
        <td><?php echo round($summary['current_value_USDT'], 2); ?></td>
    </tr>
    <tr>
        <td>Diff</td>
        <td><?php echo round($summary['value_diff_USDT'], 2); ?></td>
    </tr>
</table>
