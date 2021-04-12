<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PendingOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pending Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pending-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Pending Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'options' => [
            'class' => 'table-responsive',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'contentOptions'=>[ 'style'=>'width: 60px']
            ],
            [
                'attribute' => 'id',
                'contentOptions'=>[ 'style'=>'width: 100px'],
            ],
            [
                'attribute' => 'exchange',
                'contentOptions'=>[ 'style'=>'width: 100px'],
            ],
            [
                'attribute' => 'market',
                'contentOptions'=>[ 'style'=>'width: 100px'],
            ],
            [
                'attribute' => 'quantity',
                'contentOptions'=>[ 'style'=>'width: 100px'],
            ],
            [
                'attribute' => 'price',
                'value' => function($model) {
                    return number_format($model->price, 8, '.', false);
                    },
                'contentOptions' => ['style'=>'width: 120px'],
            ],
            ['attribute' => 'value',
                'value' => function($model) {
                    return round($model->value,3);
                }
            ],
            [
                'attribute' => 'condition',
                'contentOptions'=>[ 'style'=>'width: 120px'],
            ],
            [
                'attribute' => 'type',
                'contentOptions'=>[ 'style'=>'width: 60px'],
            ],
            [
                'attribute' => 'stop_loss',
                'value' => function($model) {
                    if ($model->stop_loss > 0) {
                        $val = number_format($model->stop_loss, 8);
                    } else {
                        $val = null;
                    }
                        return $val;
                    },
                'contentOptions'=>[ 'style'=>'width: 120px'],
            ],
            ['attribute' => 'take_profit',
                'value' => function($model) {
                    if ($model->take_profit > 0) {
                        $val = number_format($model->take_profit, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                },
                'contentOptions'=>[ 'style'=>'width: 120px'],
                ],
            'uuid',
            [
                'attribute' => 'transaction_type',
                'label' => 'Type',
                'contentOptions'=>[ 'style'=>'width: 120px'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions'=> ['style'=>'width: 100px']
            ],
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>
