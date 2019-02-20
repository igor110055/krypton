<?php

use yii\grid\GridView;

$this->title = 'Krypton - Lags';

?>
<div class="lags-index">

    <div class="body-content">
        <h2>Diff = Binance - Bittrex</h2>
        <div class="row">
        <?php
            echo GridView::widget([
                'dataProvider' => $provider,
            ]);
        ?>
        </div>
    </div>
</div>
