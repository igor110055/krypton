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
                    return round($model->value,3);
            }],
            'condition',
            'type',
            ['attribute' => 'stop_loss',
                'value' => function($model) {
                    if ($model->stop_loss > 0) {
                        $val = number_format($model->stop_loss, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            ['attribute' => 'take_profit',
                'value' => function($model) {
                    if ($model->take_profit > 0) {
                        $val = number_format($model->take_profit, 8);
                    } else {
                        $val = null;
                    }
                    return $val;
                }],
            'uuid',
            'transaction_type',

            ['class' => 'yii\grid\ActionColumn'],
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>
