<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PendingOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pending-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'market') ?>

    <?= $form->field($model, 'quantity') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'stop_loss') ?>

    <?php // echo $form->field($model, 'start_earn') ?>

    <?php // echo $form->field($model, 'last_bid') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
