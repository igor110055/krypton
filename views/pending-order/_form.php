<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PendingOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pending-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'uuid')->textInput() ?>

    <?= $form->field($model, 'market')->dropdownList(
        $model->getMarketList(), ['prompt'=>'Select market']
    ) ?>
    <div id="actualPrice"></div>

    <?= $form->field($model, 'value')->textInput() ?>
    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>
    <div class="btn btn-primary" id="calcQtyBtn">Calc quantity</div>
    <div class="btn btn-primary" id="calcValBtn">Calc value</div>

    <?= $form->field($model, 'type')->dropDownList([
        'SELL' => 'SELL',
        'BUY' => 'BUY'
    ]) ?>

    <?= $form->field($model, 'condition')->dropDownList([
        'COND_MORE' => $model::COND_MORE,
        'COND_LESS' => $model::COND_LESS
    ]) ?>

    <?= $form->field($model, 'stop_loss')->textInput() ?>

    <?= $form->field($model, 'take_profit')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$script = <<< JS

$('#pendingorder-market').change(function (data) {
    var market = this.value;
    $.get( "/ajax/get-ticker", { market: market }, function( data ) {
        var html = '<table>' +
         '<tr><td>Ask</td><td>' + data.result.Ask + '</td></tr>' +
         '<tr><td>Bid</td><td>' + data.result.Bid + '</td></tr>' +
         '<tr><td>Last</td><td>' + data.result.Last + '</td></tr>' +
          '</table>';
        $('#actualPrice').html(html);
        $('#pendingorder-price').val(data.result.Last);
    });
})

$('#calcQtyBtn').click(function() {

  var value = $('#pendingorder-value').val();
  var price = $('#pendingorder-price').val();
  
  if (value && price) {
      var qty = value / price;
      $('#pendingorder-quantity').val(parseFloat(qty).toFixed(2));
  }
})

$('#calcValBtn').click(function() {

  var qty = $('#pendingorder-quantity').val();
  var price = $('#pendingorder-price').val();
  
  if (qty && price) {
      var value = qty * price;
      $('#pendingorder-value').val(parseFloat(value).toFixed(8));
  }
})

JS;
$position = \yii\web\View::POS_READY;
$this->registerJs($script, $position);
