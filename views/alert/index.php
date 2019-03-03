<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AlertSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Alerts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Alert', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'market',
            ['attribute' => 'price',
             'value' => function($model) {
                return number_format($model->price, 8);
            }],
            'condition',
            'message:ntext',
            'is_active',
            //'modified',
            //'crdate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
