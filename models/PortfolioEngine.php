<?php

namespace app\models;

use app\models\BotEngine;

class PortfolioEngine
{
    private $botEngine;
    private $currentPrices;

    public function __construct(){

        $this->botEngine = new BotEngine();
//        $this->botEngine->prepareCurrentPrices();
//        $this->currentPrices = $this->botEngine->getMarketLastBids();

    }
    public function handleTickerMonitor()
    {
        $summary = $this->botEngine->getExchangesSummaries();
        var_dump($summary);

        $portfolioTicker = new PortfolioTicker();
        $portfolioTicker->created_at = date('Y-m-d H:i:s');
        $portfolioTicker->hodl_btc_value = 0;

    }
}