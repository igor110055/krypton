<?php

namespace app\models;

use app\utils\Currency;

class PortfolioEngine
{
    private $botEngine;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = new Configuration();
        $this->botEngine = new BotEngine();
    }
    public function handleTickerMonitor(): void
    {
        $summary = $this->botEngine->getExchangesSummaries();
        $hodlBtcValue = 0;
        $exchangeBtcValue = $summary['Binance']['sumBTC'] + $summary['Bittrex']['sumBTC'];

        $btcValue = $hodlBtcValue + $exchangeBtcValue;
        $hodlPercent = 0;
        $btcPrice = $summary['Binance']['summary']['BTC']['PriceUSDT'];
        $usdPrice = Currency::getUsdToPlnRate();
        $deposit = $this->configuration->getValue('pln_deposit');

        $usdValue = $btcValue * $btcPrice;
        $plnValue = round($usdValue * $usdPrice, 2);

        $plnDiff = $plnValue - $deposit;
        $percentDiff = round($plnDiff / $deposit * 100, 2);

        $portfolioTicker = new PortfolioTicker();
        $portfolioTicker->created_at = date('Y-m-d H:i:s');
        $portfolioTicker->hodl_btc_value = 0;
        $portfolioTicker->exchange_btc_value = number_format($exchangeBtcValue, 8);
        $portfolioTicker->hodl_percent = $hodlPercent;
        $portfolioTicker->btc_price = $summary['Binance']['summary']['BTC']['PriceUSDT'];
        $portfolioTicker->usd_price = Currency::getUsdToPlnRate();
        $portfolioTicker->deposit = $deposit;
        $portfolioTicker->pln_diff = $plnDiff;
        $portfolioTicker->change = $percentDiff;
        $portfolioTicker->btc_value = $btcValue;
        $portfolioTicker->usdt_value = $usdValue;
        $portfolioTicker->pln_value = $plnValue;

        $portfolioTicker->save();
    }
}