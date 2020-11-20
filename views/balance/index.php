<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
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
            'footer' => number_format($binanceSum, 8)
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
