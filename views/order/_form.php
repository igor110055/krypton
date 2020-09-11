<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'market')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>
    <?= $form->field($model, 'sell_price')->textInput() ?>

    <?= $form->field($model, 'value')->textInput() ?>
    <?= $form->field($model, 'sell_value')->textInput() ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stop_loss')->textInput() ?>

    <?= $form->field($model, 'take_profit')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([
        $model::STATUS_OPEN => $model::STATUS_OPEN,
        $model::STATUS_CLOSED => $model::STATUS_CLOSED,
        $model::STATUS_PROCESSED => $model::STATUS_PROCESSED,
        $model::STATUS_DONE => $model::STATUS_DONE,
    ]) ?>

    <?= $form->field($model, 'transaction_type')->dropDownList([
        $model::TRANSACTION_BEST => 'Best',
        $model::TRANSACTION_STRICT => 'Strict'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
