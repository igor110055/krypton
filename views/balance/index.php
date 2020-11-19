<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<h1>Binance Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $binanceBalanceProvider,
    'showFooter' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'Currency',
        'Balance',
        [
            'attribute' => 'Price',
            'value' => function($data) {
                return number_format($data['Price'], 8);
            }
        ],
        [
            'attribute' => 'Value',
            'value' => function($data) {
                return number_format($data['Value'], 8);
            },
            'footer' => number_format($binanceSum, 8)
        ],
    ],
]); ?>

<h1>Bittrex Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $bittrexBalanceProvider,
    'showFooter' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'Currency',
        'Balance',
        [
            'attribute' => 'Price',
            'value' => function($data) {
                return number_format($data['Price'], 8);
            }
        ],
        [
            'attribute' => 'Value',
            'value' => function($data) {
                return number_format($data['Value'], 8);
            },
            'footer' => number_format($bittrexSumValue, 8)
        ],
    ],
]); ?>
