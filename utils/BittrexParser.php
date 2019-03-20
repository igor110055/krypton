<?php

namespace app\utils;

use Yii;

class BittrexParser
{
    public static function getMarketList($marketJson)
    {
        $marketData = json_decode($marketJson, true);

        $marketList = [];
        foreach ($marketData['result'] as $market) {
            if ($market['BaseCurrency'] == 'BTC'){
                $marketList[$market['MarketName']] = $market['MarketCurrencyLong'] . ' (' . $market['MarketCurrency'] . ')';
            }
        }

        asort($marketList);
        return $marketList;
    }

    public static function getPricesFromSummaries($marketSummaries)
    {
        foreach ($marketSummaries['result'] as $marketSummary) {
            if (strstr($marketSummary['MarketName'], 'BTC')){
                $marketLastBids[$marketSummary['MarketName']] = $marketSummary['Last'];
            }
        }

        return $marketLastBids;
    }
}