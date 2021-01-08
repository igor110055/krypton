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
            if (strstr($marketSummary['MarketName'], 'BTC') || strstr($marketSummary['MarketName'], 'USDT')){
                $marketLastBids[$marketSummary['MarketName']] = $marketSummary['Last'];
            }
        }

        return $marketLastBids;
    }

    public static function getSummary(array $bittrexBalance, array $currentPrices): array
    {
//        echo'<pre>';var_dump($currentPrices);exit;
        $bittrexSummary = [];
        $bittrexSumValue = 0;
        $bittrexSumValueUSDT = 0;

        foreach ($bittrexBalance as $asset) {

            if ($asset['Currency'] != 'BTC' && isset($currentPrices['BTC-' . $asset['Currency']])) {
                $value = $asset['Balance'] * $currentPrices['BTC-' . $asset['Currency']];
                if ($value > 0.0001) {
                    $bittrexSummary[$asset['Currency']]['Currency'] = $asset['Currency'];
                    $bittrexSummary[$asset['Currency']]['Balance'] = $asset['Balance'];
                    $bittrexSummary[$asset['Currency']]['Price'] = $currentPrices['BTC-' . $asset['Currency']];
                    $priceUSDT = $currentPrices['USDT-' . $asset['Currency']] ?? 0;
                    $bittrexSummary[$asset['Currency']]['PriceUSDT'] = $priceUSDT;
                    $bittrexSummary[$asset['Currency']]['Value'] = $value;
                    $bittrexSummary[$asset['Currency']]['ValueUSDT'] = $bittrexSummary[$asset['Currency']]['Balance'] * $bittrexSummary[$asset['Currency']]['PriceUSDT'];
                    $bittrexSumValue += $value;
                    $bittrexSumValueUSDT += $bittrexSummary[$asset['Currency']]['ValueUSDT'];
                }
            }
            if ($asset['Currency'] == 'BTC') {
                $bittrexSummary['BTC']['Currency'] = 'BTC';
                $bittrexSummary['BTC']['Balance'] = $asset['Balance'];
                $bittrexSummary['BTC']['Price'] = 0;
                $bittrexSummary['BTC']['PriceUSDT'] = (float)$currentPrices['USDT-BTC'];
                $bittrexSummary['BTC']['Value'] = $asset['Balance'];
                $bittrexSummary['BTC']['ValueUSDT'] =  $bittrexSummary['BTC']['Balance'] * $bittrexSummary['BTC']['PriceUSDT'];
                $bittrexSumValue += $asset['Balance'];
                $bittrexSumValueUSDT += $bittrexSummary['BTC']['ValueUSDT'];
            }
            if ($asset['Currency'] == 'USDT') {
                $bittrexSummary['USDT']['Currency'] = 'USDT';
                $bittrexSummary['USDT']['Balance'] = $asset['Balance'];
                $bittrexSummary['USDT']['Price'] = 1 / (float)$currentPrices['USDT-BTC'];
                $bittrexSummary['USDT']['PriceUSDT'] = 1;
                $bittrexSummary['USDT']['Value'] = $bittrexSummary['USDT']['Balance'] * $bittrexSummary['USDT']['Price'];
                $bittrexSummary['USDT']['ValueUSDT'] = $bittrexSummary['USDT']['Balance'] * $bittrexSummary['USDT']['PriceUSDT'];
                $bittrexSumValue += $bittrexSummary['USDT']['Value'];
                $bittrexSumValueUSDT += $bittrexSummary['USDT']['ValueUSDT'];
            }
        }

        return [
            'summary' => $bittrexSummary,
            'sumBTC' => $bittrexSumValue,
            'sumUSDT' => $bittrexSumValueUSDT,
        ];
    }
}