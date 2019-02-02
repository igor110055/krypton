<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PendingOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pending-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'market')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'stop_loss')->textInput() ?>

    <?= $form->field($model, 'start_earn')->textInput() ?>

    <?= $form->field($model, 'last_bid')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
