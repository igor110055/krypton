<?php

namespace app\utils;

use Yii;

class BittrexParser extends ExchangeParser
{
    public static function getMarketList($marketData)
    {
        $marketList = [];
        foreach ($marketData['result'] as $market) {
            if ($market['BaseCurrency'] == 'BTC'){
                $marketList[$market['MarketName']] = $market['MarketCurrencyLong'] . ' (' . $market['MarketCurrency'] . ')';
            }
        }

        asort($marketList);
        return $marketList;
    }

    public static function getPricesFromSummaries(array $marketSummaries)
    {
        foreach ($marketSummaries['result'] as $marketSummary) {
            if (strstr($marketSummary['MarketName'], 'BTC')){
                $marketLastBids[$marketSummary['MarketName']] = $marketSummary['Last'];
            }
        }

        return $marketLastBids;
    }

    public static function getBittrexSummary(array $bittrexBalance, array $currentPrices): array
    {
        $bittrexSummary = [];
        $bittrexSumValue = 0;

        foreach ($bittrexBalance as $crypto) {

            if ($crypto['Currency'] != 'BTC' && isset($currentPrices['Bittrex']['BTC-' . $crypto['Currency']])) {
                $value = $crypto['Balance'] * $currentPrices['Bittrex']['BTC-' . $crypto['Currency']];
                if ($value > 0.0001) {
                    $bittrexSummary[$crypto['Currency']]['Currency'] = $crypto['Currency'];
                    $bittrexSummary[$crypto['Currency']]['Balance'] = $crypto['Balance'];
                    $bittrexSummary[$crypto['Currency']]['Price'] = $currentPrices['Bittrex']['BTC-' . $crypto['Currency']];
                    $bittrexSummary[$crypto['Currency']]['Value'] = $value;
                    $bittrexSumValue += $value;
                }
            }
            if ($crypto['Currency'] == 'BTC') {
                $bittrexSummary['BTC']['Currency'] = 'BTC';
                $bittrexSummary['BTC']['Balance'] = $crypto['Balance'];
                $bittrexSummary['BTC']['Price'] = 0;
                $bittrexSummary['BTC']['Value'] = $crypto['Balance'];
                $bittrexSumValue += $crypto['Balance'];
            }
        }

        return [
            'bittrexSummary' => $bittrexSummary,
            'bittrexSumValue' => $bittrexSumValue
        ];
    }
}