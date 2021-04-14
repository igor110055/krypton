<?php

use app\models\Configuration;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $binanceBalanceProvider ArrayDataProvider */
/* @var $bittrexBalanceProvider ArrayDataProvider */
/* @var $bittrexSumValue float */
/* @var $bittrexSumValueUSDT float */
/* @var $binanceSumValue float */
/* @var $binanceSumValueUSDT float */
/* @var $btcPrice array */
/* @var $plnPrice float */
/* @var $configuration Configuration */
/* @var $hodlBTCvalueSum float */

$this->title = "Balance";

$usdtStaked = $configuration->getValue('usdt_staked');
$plnStaked = $usdtStaked * $plnPrice;

$btcSumValue = $binanceSumValue + $bittrexSumValue + $hodlBTCvalueSum;
$usdValue = $btcSumValue * $btcPrice['Last'];
$plnValue = $usdValue * $plnPrice;
$plnDiff = $plnValue -  $configuration->getValue('pln_deposit');
$percentDiff = round($plnDiff / $configuration->getValue('pln_deposit') * 100, 2);

$summaryUsd = $usdValue + $usdtStaked;
$summaryValue = $summaryUsd * $plnPrice;


?>
<h1>Summary</h1>
<div style="display: flex">
    <div style="width: 45%; margin-right:5%">
        <table class="table table-striped table-bordered">
            <tr>
                <th style="width: 50%">BTC value</th>
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
        <br />
        <br />
        <table class="table table-striped table-bordered">
            <tr>
                <th style="width: 50%">USD summary</th>
                <td><?php echo round($summaryUsd,2) ?></td>
            </tr>
            <tr>
                <th>PLN summary</th>
                <td><?php echo round($summaryValue,2) ?></td>
            </tr>
        </table>
    </div>
    <div style="width: 45%; margin-left:5%">
        <table class="table table-striped table-bordered">
            <tr>
                <th style="width: 50%">PLN deposit</th>
                <td><?php echo $configuration->getValue('pln_deposit') ?></td>
            </tr>
            <tr>
                <th>PLN profit</th>
                <td><?php echo round($plnDiff, 2) ?></td>
            </tr>
            <tr>
                <th>% profit</th>
                <td><?php echo $percentDiff ?></td>
            </tr>
        </table>
        <br />
        <br />
        <table class="table table-striped table-bordered">
            <tr>
                <th style="width: 50%">USDT staked</th>
                <td><?php echo round($usdtStaked, 2) ?></td>
            </tr>
            <tr>
                <th>PLN staked</th>
                <td><?php echo round($plnStaked, 2) ?></td>
            </tr>
        </table>
    </div>
</div>

<h1>Hodl Balance</h1>
BTC: <?php echo $hodlBTCvalueSum; ?>

<h1>Binance Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $binanceBalanceProvider,
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'options' => [
        'class' => 'table-responsive',
    ],
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
            'contentOptions'=> ['style'=>'width: 12%;']
        ],
        [
            'attribute' => 'PriceUSDT',
            'value' => function($data) {
                return number_format($data['PriceUSDT'], 4, '.', '');
            },
            'contentOptions'=> ['style'=>'width: 12%;']
        ],
        [
            'attribute' => 'Value',
            'value' => function($data) {
                return number_format($data['Value'], 8);
            },
            'contentOptions'=> ['style'=>'width: 12%;'],
            'footer' => number_format($binanceSumValue, 8)
        ],
        [
            'attribute' => 'ValueUSDT',
            'value' => function($data) {
                return number_format($data['ValueUSDT'], 4, '.', '');
            },
            'contentOptions'=> ['style'=>'width: 12%;'],
            'footer' => number_format($binanceSumValueUSDT, 4, '.', '')
        ],
    ],
]); ?>

<h1>Bittrex Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $bittrexBalanceProvider,
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'options' => [
        'class' => 'table-responsive',
    ],
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
            'contentOptions'=> ['style'=>'width: 12%;']
        ],
        [
            'attribute' => 'PriceUSDT',
            'value' => function($data) {
                return number_format($data['PriceUSDT'], 4, '.', '');
            },
            'contentOptions'=> ['style'=>'width: 12%;']
        ],
        [
            'attribute' => 'Value',
            'value' => function($data) {
                return number_format($data['Value'], 8);
            },
            'contentOptions'=> ['style'=>'width: 12%;'],
            'footer' => number_format($bittrexSumValue, 8)
        ],
        [
            'attribute' => 'ValueUSDT',
            'value' => function($data) {
                return number_format($data['ValueUSDT'], 4, '.', '');
            },
            'contentOptions'=> ['style'=>'width: 12%;'],
            'footer' => number_format($bittrexSumValueUSDT, 4, '.', '')
        ],
    ],
]); ?>
