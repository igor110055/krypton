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
            'market',
            'quantity',
            ['attribute' => 'price',
                'value' => function($model) {
                    return number_format($model->price, 8);
            }],
            //'value',
            'type',
            'stop_loss',
            'start_earn',
            'status',
            //'crdate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
