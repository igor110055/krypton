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

    <?= $form->field($model, 'exchange')->dropdownList(
        ['Bittrex' => 'Bittrex', 'Binance' => 'Binance'], ['prompt'=>'Select exchange']
    ) ?>

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
        'BUY' => 'BUY',
        'SELL' => 'SELL'
    ]) ?>

    <?= $form->field($model, 'condition')->dropDownList([
        'COND_LESS' => $model::COND_LESS,
        'COND_MORE' => $model::COND_MORE
    ]) ?>

    <?= $form->field($model, 'stop_loss')->textInput() ?>

    <?= $form->field($model, 'take_profit')->textInput() ?>

    <?= $form->field($model, 'transaction_type')->dropDownList([
        $model::TRANSACTION_BEST => 'Best',
        $model::TRANSACTION_STRICT => 'Strict'
    ]) ?>

    <?= $form->field($model, 'uuid')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$script = <<< JS

$('#pendingorder-exchange').change(function (data) {
    var exchange = this.value;
    var html = '<option>Select market</option>';
    $.get( "/ajax/get-markets", { exchange: exchange }, function( data ) {
        $.each(data, function(index, value) {
            html = html + '<option value="' + index + '">' + value + '</option>';
        });
        $('#pendingorder-market').html(html);
    });
});

$('#pendingorder-market').change(function (data) {
    var market = this.value;
    var exchange = $('#pendingorder-exchange').val();
    $.get( "/ajax/get-ticker", { exchange: exchange, market: market }, function( data ) {
        var html = '<table>' +
         '<tr><td>Ask</td><td>' + parseFloat(data.Ask).toFixed(8) + '</td></tr>' +
         '<tr><td>Bid</td><td>' + parseFloat(data.Bid).toFixed(8) + '</td></tr>' +
         '<tr><td>Last</td><td>' + parseFloat(data.Last).toFixed(8) + '</td></tr>' +
          '</table>';
        $('#actualPrice').html(html);
        $('#pendingorder-price').val(parseFloat(data.Bid).toFixed(8));
        $('#pendingorder-value').val(0.01);
        calcQty();
        var stop_loss = data.Bid - (data.Bid * 0.02);
        var take_profit = data.Bid + (data.Bid * 0.02);
        $('#pendingorder-stop_loss').val(parseFloat(stop_loss).toFixed(8));
        $('#pendingorder-take_profit').val(parseFloat(take_profit).toFixed(8));
    });
})

$('#calcQtyBtn').click(function() {
    calcQty();
});

$('#calcValBtn').click(function() {

  var qty = $('#pendingorder-quantity').val();
  var price = $('#pendingorder-price').val();
  
  if (qty && price) {
      var value = qty * price;
      $('#pendingorder-value').val(parseFloat(value).toFixed(8));
  }
});

function calcQty() {
  var value = $('#pendingorder-value').val();
  var price = $('#pendingorder-price').val();
  
  if (value && price) {
      var qty = value / price;
      $('#pendingorder-quantity').val(parseFloat(qty).toFixed(2));
  }
}

JS;
$position = \yii\web\View::POS_READY;
$this->registerJs($script, $position);
