<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Oscylators';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-oscylators">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div id="start-chart">
            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container">
                <div class="tradingview-widget-container__widget"></div>
                <div class="tradingview-widget-copyright"><a href="https://pl.tradingview.com/markets/cryptocurrencies/prices-all/" rel="noopener" target="_blank"><span class="blue-text">Rynki Kryptowalut</span></a> od TradingView</div>
                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-screener.js" async>
                    {
                        "width": "100%",
                        "height": "100%",
                        "defaultColumn": "oscillators",
                        "screener_type": "crypto_mkt",
                        "displayCurrency": "USD",
                        "locale": "pl",
                        "transparency": false
                    }
                </script>
            </div>
            <!-- TradingView Widget END -->
        </div>

    </div>
</div>
