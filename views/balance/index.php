<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<h1>Balance</h1>

<?php echo GridView::widget([
    'dataProvider' => $balanceProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'Currency',
        'Balance',
        'Value',
        'Available',
        'Pending',
        'CryptoAddress'
    ],
]); ?>
