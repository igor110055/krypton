<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Alert */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="alert-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'market')->dropdownList(
        $model->getMarketList(), ['prompt'=>'Select market']
    ) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'condition')->dropDownList([
            'COND_MORE' => $model::COND_MORE,
            'COND_LESS' => $model::COND_LESS
    ]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
