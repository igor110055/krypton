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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'market',
            'quantity',
            ['attribute' => 'price',
                'value' => function($model) {
                    return number_format($model->price, 8);
            }],
            ['attribute' => 'value',
                'value' => function($model) {
                    $val = $model->price * $model->quantity;
                    return round($val,3);
            }],
            'condition',
            'type',
            'stop_loss',
            'start_earn',
            'uuid',

            ['class' => 'yii\grid\ActionColumn'],
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>
