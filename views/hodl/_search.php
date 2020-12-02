<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\HodlPositionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hodl-position-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'buy_date') ?>

    <?= $form->field($model, 'sell_date') ?>

    <?= $form->field($model, 'market') ?>

    <?= $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'buy_price') ?>

    <?php // echo $form->field($model, 'sell_price') ?>

    <?php // echo $form->field($model, 'buy_value') ?>

    <?php // echo $form->field($model, 'sell_value') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'val_diff') ?>

    <?php // echo $form->field($model, 'price_diff') ?>

    <?php // echo $form->field($model, 'pln_value') ?>

    <?php // echo $form->field($model, 'pln_diff_value') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
