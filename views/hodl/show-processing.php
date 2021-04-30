<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\HodlPositionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $summary array */

$this->title = 'Hodl processing positions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hodl-position-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterPosition' => GridView::FILTER_POS_HEADER,
        'showFooter' => true,
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
                'attribute' => 'portfolio',
                'value' => 'portfolio.name'
            ],
            [
                'attribute' => 'buy_date',
                'filter' => false
            ],
            'market',
            [
                'attribute' => 'quantity',
                'filter' => false,
                'footer' => round($summary['quantity'], 8)
            ],
            [
                'attribute' => 'buy_price',
                'filter' => false,
                'footer' => round($summary['avg_price'], 2)
            ],
            [
                'attribute' => 'sell_price',
                'label' => 'Current price',
                'filter' => false
            ],
            [
                'attribute' => 'buy_value',
                'filter' => false,
                'footer' => round($summary['buy_value'], 2)
            ],
            [
                'attribute' => 'sell_value',
                'label' => 'Current value',
                'filter' => false,
                'footer' => round($summary['sell_value'], 2)
            ],
            [
                'attribute' => 'val_diff',
                'value' => function ($model){
                    return round($model->val_diff, 4);
                },
                'filter' => false,
                'footer' => round($summary['val_diff'], 2)
            ],
            [
                'attribute' => 'pln_buy_value',
                'filter' => false,
                'footer' => round($summary['pln_buy_value'], 2),
                'value' => function ($model) {
                    return round($model->pln_buy_value, 2);
                }
            ],
            [
                'attribute' => 'pln_value',
                'filter' => false,
                'footer' => round($summary['pln_value'], 2),
                'value' => function ($model) {
                    return round($model->pln_value, 2);
                }
            ],
            [
                'attribute' => 'pln_diff_value',
                'filter' => false,
                'footer' => round($summary['pln_diff_value'], 2),
                'value' => function ($model) {
                    return round($model->pln_diff_value, 2);
                }
            ],
            [
                'attribute' => 'price_diff',
                'filter' => false,
                'value' => function ($model) {
                    return round($model->price_diff, 2);
                },
                'footer' => round($summary['global_price_diff'], 2),
            ],
            'comment',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function($url, $model, $key) {
                        $url = '/hodl/update?id=' . $model->id;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
