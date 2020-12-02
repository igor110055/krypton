<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\HodlPosition */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Hodl Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hodl-position-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'buy_date',
            'sell_date',
            'market',
            'quantity',
            'buy_price',
            'sell_price',
            'buy_value',
            'sell_value',
            'status',
            'val_diff',
            'price_diff',
            'pln_value',
            'pln_diff_value',
            'comment',
        ],
    ]) ?>

</div>
