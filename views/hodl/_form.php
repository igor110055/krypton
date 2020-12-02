<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\HodlPosition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hodl-position-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'buy_date')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Put time ...'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 'sell_date')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Put time ...'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 'market')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'buy_price')->textInput() ?>

    <?= $form->field($model, 'sell_price')->textInput() ?>

    <?= $form->field($model, 'buy_value')->textInput() ?>

    <?= $form->field($model, 'sell_value')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([
        $model::STATUS_PROCESSING => $model::STATUS_PROCESSING,
        $model::STATUS_DONE => $model::STATUS_DONE,
    ]) ?>

    <?= $form->field($model, 'val_diff')->textInput() ?>

    <?= $form->field($model, 'price_diff')->textInput() ?>

    <?= $form->field($model, 'pln_value')->textInput() ?>

    <?= $form->field($model, 'pln_diff_value')->textInput() ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
