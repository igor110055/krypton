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

    <?php Pjax::begin(['id' => 'processing-table', 'timeout' => false, 'enablePushState' => false]); ?>
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
//            [
//                'attribute' => 'id',
//                'filter' => false
//            ],
            [
                'attribute' => 'exchange',
                'filter' => ['binance' => 'Binance', 'bittrex' => 'Bittrex'],
            ],
            [
                'attribute' => 'market',
                'filter' => ['btc' => 'BTC', 'usdt' => 'USDT'],
            ],
            [
                'attribute' => 'price_diff',
                'label' => 'Price %',
                'footer' => round($summary['global_price_diff'], 2),
            ],
            [
                'attribute' => 'value_diff_usdt',
                'label' => 'Val diff $',
                'value' => function ($model) {
                    return round($model->value_diff_usdt, 4);
                },
                'footer' => round($summary['value_diff_usdt'], 4)
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
                'label' => 'Curr Price',
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
                'label' => 'Val',
                'filter' => false,
                'footer' => round($summary['value'], 8)
            ],
            [
                'attribute' => 'value_usdt',
                'label' => 'Val USDT',
                'filter' => false,
                'value' => function ($model) {
                    return round($model->value_usdt, 4);
                },
                'footer' => round($summary['summary_value_usdt'], 4)
            ],
            [
                'attribute' => 'current_value',
                'label' => 'Curr Val',
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
                'attribute' => 'current_value_usdt',
                'filter' => false,
                'label' => 'CV usdt',
                'value' => function ($model) {
                    return round($model->current_value_usdt, 4);
                },
                'footer' => round($summary['summary_current_value_usdt'], 4)
            ],
            [
                'attribute' => 'stop_loss',
                'format' => 'raw',
                'value' => function ($model){
                    $val = null;
                    if ($model->stop_loss > 0) {
                        if (strstr($model->market, 'BTC')) {
                            $val = number_format($model->stop_loss, 8, '.', '');
                        } else {
                            $val = number_format($model->stop_loss, 4, '.', '');
                        }
                    }
                    $key = (string)$model->uuid;
                    return Html::textInput("stop_loss[$key]", $val, ['style' => 'width: 100px', 'class' => 'stop-loss-input']);
                },
                'filter' => false,
                'footer' => '<button id="set-stop-loss">Set</button>'
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
            'crdate',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete} {sell}',
                'buttons' => [
                    'sell' => function ($url, $model, $key) {
                        return Html::a('Sell', $url, ['data-pjax' => 0]);
                    }
                ]
            ],
        ],
    ]);

    ?>
    <?php Pjax::end(); ?>
</div>
<?php

$script = <<<JS
$('#processing-table').on('click', '#set-stop-loss', function(e) {
    // e.preventDefault();
    let data = $('.stop-loss-input').serialize();
    $.ajax({
        url: '/order/update-stop-loss',
        type: 'POST',
        dataType: 'json',
        data: data,
        success : function(result) {
            console.log(result);
        }
    }).done(function(data) {
        $.pjax.reload({container:"#processing-table"});
    });
 });

setInterval(function(){ $.pjax.reload({container:"#processing-table"}); }, 10000);
JS;


$this->registerJs($script, \yii\web\View::POS_READY);
?>