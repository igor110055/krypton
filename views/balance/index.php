<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $binanceBalanceProvider yii\data\ArrayDataProvider */
/* @var $bittrexBalanceProvider yii\data\ArrayDataProvider */
/* @var $bittrexSumValue float */
/* @var $binanceSumValue float */
/* @var $btcPrice array */
/* @var $plnPrice float */

$btcSumValue = $binanceSumValue + $bittrexSumValue;
$usdValue = $btcSumValue * $btcPrice['Last'];
$plnValue = $usdValue * $plnPrice;
?>
<h1>Summary</h1>
<div style="width: 50%">
    <table class="table table-striped table-bordered">
        <tr>
            <th>BTC value</th>
            <td><?php echo round($btcSumValue, 8) ?></td>
        </tr>
        <tr>
            <th>USD value</th>
            <td><?php echo round($usdValue,2) ?></td>
        </tr>
        <tr>
            <th>PLN value</th>
            <td><?php echo round($plnValue,2) ?></td>
        </tr>
    </table>
</div>

<h1>Binance Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $binanceBalanceProvider,
    'options' => ['style' => 'width:100%'],
    'showFooter' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'Currency',
            'contentOptions'=> ['style'=>'width: 24%;']
        ],
        [
            'attribute' => 'Balance',
            'contentOptions'=> ['style'=>'width: 24%;']
        ],
        [
            'attribute' => 'Price',
            'value' => function($data) {
                return number_format($data['Price'], 8);
            },
            'contentOptions'=> ['style'=>'width: 24%;']
        ],
        [
            'attribute' => 'Value',
            'value' => function($data) {
                return number_format($data['Value'], 8);
            },
            'contentOptions'=> ['style'=>'width: 24%;'],
            'footer' => number_format($binanceSumValue, 8)
        ],
    ],
]); ?>

<h1>Bittrex Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $bittrexBalanceProvider,
    'options' => ['style' => 'width:100%'],
    'showFooter' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'Currency',
            'contentOptions'=> ['style'=>'width: 24%;']
        ],
        [
            'attribute' => 'Balance',
            'contentOptions'=> ['style'=>'width: 24%;']
        ],
        [
            'attribute' => 'Price',
            'value' => function($data) {
                return number_format($data['Price'], 8);
            },
            'contentOptions'=> ['style'=>'width: 24%;']
        ],
        [
            'attribute' => 'Value',
            'value' => function($data) {
                return number_format($data['Value'], 8);
            },
            'contentOptions'=> ['style'=>'width: 24%;'],
            'footer' => number_format($bittrexSumValue, 8)
        ],
    ],
]); ?>
