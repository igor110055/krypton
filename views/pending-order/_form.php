<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PendingOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pending-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'market')->dropdownList(
        $model->getMarketList(), ['prompt'=>'Select market']
    ) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([
        'SELL' => 'SELL',
        'BUY' => 'BUY'
    ]) ?>

    <?= $form->field($model, 'condition')->dropDownList([
        'COND_MORE' => $model::COND_MORE,
        'COND_LESS' => $model::COND_LESS
    ]) ?>

    <?= $form->field($model, 'stop_loss')->textInput() ?>

    <?= $form->field($model, 'start_earn')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
