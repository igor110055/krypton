<?php
namespace app\utils;

use app\models\Api\Bittrex;
use Yii;

class BinanceParser extends ExchangeParser
{
    public static function parsePricesForLag($prices)
    {
        foreach ($prices as $marketPrice) {
            if (strpos($marketPrice['symbol'], 'BTC', -3)){
                $market = str_replace('BTC', '', $marketPrice['symbol']);
                $fullMarket = 'BTC-'.$market;
                $marketPrices[$fullMarket] = $marketPrice['price'];
            }
        }

        return $marketPrices;
    }

    public static function parsePrices(array $prices): array
    {
        $marketPrices = [];
        foreach ($prices as $marketPrice) {
            if (strpos($marketPrice['symbol'], 'BTC', -3) || strpos($marketPrice['symbol'], 'USDT', -4)){
                $marketPrices[$marketPrice['symbol']] = $marketPrice['price'];
            }
        }

        return $marketPrices;
    }

    public static function sliceTicker(array $tickerData, array $markets)
    {
        $slicedTickers = [];
        foreach ($tickerData as $ticker) {
            if (strpos($ticker['symbol'], 'BTC', -3)){
                $market = str_replace('BTC', '', $ticker['symbol']);
                $fullMarket = 'BTC-'.$market;
                $ticker['bittrexName'] = $fullMarket;
                if (in_array($fullMarket, $markets)) {
                    $slicedTickers[$fullMarket] = $ticker;
                }
            }
        }

        return $slicedTickers;
    }

    public static function formatTickerToMarketList(array $tickerData): array
    {
        $slicedTickers = [];
        foreach ($tickerData as $ticker) {
            if (strpos($ticker['symbol'], 'USDT', -4) || strpos($ticker['symbol'], 'BTC', -3)){
                $slicedTickers[$ticker['symbol']] = $ticker['symbol'];
            }
        }

        asort($slicedTickers);
        return $slicedTickers;
    }

    public static function parseTicker(array $tickerData)
    {
        $ticker = [];

        $timestamp = self::safe_integer($tickerData['closeTime']);
        $datetime = date("Y-m-d H:i:s");

        $ticker['exchange'] = 'BINANCE';
//        $ticker['symbol'] = $tickerData['symbol'];
        $ticker['symbol'] = $tickerData['bittrexName'];
        $ticker['timestamp'] = $timestamp;
        $ticker['datetime'] = $datetime;
        $ticker['high'] = self::safe_float($tickerData['highPrice']);
        $ticker['low'] = self::safe_float($tickerData['lowPrice']);
        $ticker['bid'] = self::safe_float($tickerData['bidPrice']);
        $ticker['ask'] = self::safe_float($tickerData['askPrice']);
        $ticker['vwap'] = self::safe_float($tickerData['weightedAvgPrice']);
        $ticker['open'] = self::safe_float($tickerData['openPrice']);
        $ticker['close'] = self::safe_float($tickerData['prevClosePrice']);
        $ticker['first'] = null;
        $ticker['last'] = self::safe_float($tickerData['lastPrice']);
        $ticker['change'] = self::safe_float($tickerData['priceChangePercent']);
        $ticker['percentage'] = null;
        $ticker['average'] = null;
        $ticker['basevolume'] = self::safe_float($tickerData['volume']);
        $ticker['quotevolume'] = self::safe_float($tickerData['quoteVolume']);
        $ticker['created_at'] = $datetime;
        $ticker['updated_at'] = $datetime;

        return $ticker;

    }

    public static function parseTickerForPendingOrder(array $ticker): array
    {
        $parsedTicker['Last'] = $ticker['lastPrice'];
        $parsedTicker['Ask'] = $ticker['askPrice'];
        $parsedTicker['Bid'] = $ticker['bidPrice'];

        return $parsedTicker;
    }
}