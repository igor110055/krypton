<?php
namespace app\utils;

use Yii;

class BinanceParser
{
    public static function parsePrices($prices)
    {
        foreach ($prices as $marketPrice) {
            if (strstr($marketPrice['symbol'], 'BTC')){
                $market = str_replace('BTC', '', $marketPrice['symbol']);
                $fullMarket = 'BTC-'.$market;
                $marketPrices[$fullMarket] = $marketPrice['price'];
            }
        }

        return $marketPrices;
    }
}