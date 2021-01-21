<?php
namespace app\models\Api;

use app\interfaces\ExchangeInterface;
use app\utils\BinanceParser;
use Yii;
use linslin\yii2\curl;

class Binance implements ExchangeInterface
{
    private $apiUrl = 'https://api.binance.com/';
    private $cachePath = 'datasource/api/binance/';
    private $apiSecret;
    private $apiKey;

    private $curl;

    public function __construct()
    {
        $this->apiKey = Yii::$app->params['binance']['apiKey'];
        $this->apiSecret = Yii::$app->params['binance']['secret'];
        $this->curl = new curl\Curl();
    }

    public function getPrices()
    {
        $endPoint = 'api/v3/ticker/price';

        $response = $this->getResponse($endPoint);

        return $response;
    }

    public function getExchangeInfo(): array
    {
        $endPoint = 'api/v3/exchangeInfo';

        $response = $this->getResponse($endPoint);

        return $response;
    }

    public function getTicker24($symbol = null)
    {
        $endPoint = 'api/v1/ticker/24hr';
        $params = [];

        if ($symbol) {
            $params = [
                'symbol' => $symbol
            ];
        }

        return $this->getResponse($endPoint, $params);
    }

    public function getAllOrders($symbol = null): array
    {
        $endPoint = 'api/v3/allOrders';
        $params = [];

        if ($symbol) {
            $params = [
                'symbol' => $symbol
            ];
        }

        return $this->getWithAuth($endPoint, $params);
    }

    public function getOpenOrders(string $market = null): array
    {
        $endPoint = 'api/v3/openOrders';

        return $this->getWithAuth($endPoint);
    }

    public function getMyTrades($symbol = null): array
    {
        $endPoint = 'api/v3/myTrades';
        $params = [];

        if ($symbol) {
            $params = [
                'symbol' => $symbol
            ];
        }

        return $this->getWithAuth($endPoint, $params);
    }

    public function placeBuyOrder(string $symbol, float $quantity, float $price): array
    {
        $endPoint = 'api/v3/order';

        $params['symbol'] = $symbol;
        $params['side'] = 'BUY';
        $params['type'] = 'LIMIT';
        $params['quantity'] = $quantity;
        $params['price'] = number_format($price, 8, '.', '');
        $params['timeInForce'] = 'GTC';

        \Yii::info($params, 'binance');

        $result = $this->postWithAuth($endPoint, $params);

        \Yii::info($result, 'binance');

        if (isset($result['orderId'])) {
            return [
                'success' => true,
                'orderId' => $result['orderId']
            ];
        } else {
            return [
                'success' => false,
                'msg' => $result['msg']
            ];
        }
    }

    public function placeSellOrder(string $symbol, float $quantity, float $price): array
    {
        $endPoint = 'api/v3/order';

        $params['symbol'] = $symbol;
        $params['side'] = 'SELL';
        $params['type'] = 'LIMIT';
        $params['quantity'] = $quantity;
        $params['price'] = number_format($price, 8, '.', '');
        $params['timeInForce'] = 'GTC';

        \Yii::info($params, 'binance');

        $result = $this->postWithAuth($endPoint, $params);

        \Yii::info($result, 'binance');

        if (isset($result['orderId'])) {
            return [
                'success' => true,
                'orderId' => $result['orderId']
            ];
        } else {
            return [
                'success' => false,
                'msg' => $result['msg']
            ];
        }
    }

    public function checkOrder(string $symbol, string $orderId): array
    {
        $endPoint = 'api/v3/order';
        $params['symbol'] = $symbol;
        $params['orderId'] = $orderId;

        return $this->getWithAuth($endPoint, $params);
    }

    public function getAccountInfo()
    {
        $endPoint = 'api/v3/account';

        return $this->getWithAuth($endPoint);
    }

    public function getMarketsFormatted(): array
    {
        $tickers = $this->getTicker24();
        $markets = BinanceParser::formatTickerToMarketList($tickers);

        return $markets;
    }

    public function getPricesFormatted(): array
    {
        $prices = $this->getPrices();
        $pricesFormatted = BinanceParser::parsePrices($prices);

        return $pricesFormatted;
    }

    public function getTickerFormatted(string $market): array
    {
        $ticker = $this->getTicker24($market);

        return BinanceParser::parseTickerForPendingOrder($ticker);

    }

    public function getCurrentPrice(string $market): array
    {
        $ticker = $this->getTicker24($market);

        $result = [
            'ask' => $ticker['askPrice'],
            'bid' => $ticker['bidPrice'],
            'last' => $ticker['lastPrice'],
        ];

        return $result;
    }

    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function getBalanceSummary(): array
    {
        $balance = $this->getAccountInfo();
        $currentPrices = $this->getPricesFormatted();
        $summary = BinanceParser::getSummary($balance, $currentPrices);

        return $summary;
    }

    public function getQtyPrecision(string $market): int
    {
        $step = 0;
        $exchangeInfo = $this->getExchangeInfo();

        foreach ($exchangeInfo['symbols'] as $symbol) {
            if ($symbol['symbol'] == $market) {
                $step = strpos($symbol['filters'][2]['stepSize'], '1', 0);
                if ($step > 1) {
                    $step -= 1;
                }
            }
        }

        return $step;
    }

    private function getResponse($endPoint, $params = null): array
    {
        $url = $this->getApiUrl().$endPoint;

        if (is_array($params)) {
            $url .= '?'.http_build_query($params);
        }

        $response = $this->curl->get($url);

        return json_decode($response, true);

    }

    private function getWithAuth(string $endpoint, array $params = [])
    {
        $url = $this->getApiUrl() . $endpoint;

        $params['timestamp'] = round(microtime(true) * 1000);
        $string = $this->buildQueryString($params);
        $params['signature'] = $this->generateSign($string);
        $url .= '?' . http_build_query($params);

        $this->curl->setHeaders(['X-MBX-APIKEY' => $this->apiKey]);

        $response = $this->curl->get($url);

        return json_decode($response, true);
    }

    private function postWithAuth(string $endpoint, array $params = []): array
    {
        $url = $this->getApiUrl() . $endpoint;

        $params['timestamp'] = round(microtime(true) * 1000);
        $string = $this->buildQueryString($params);
        $params['signature'] = $this->generateSign($string);
        $url .= '?' . http_build_query($params);

        $this->curl->setHeaders(['X-MBX-APIKEY' => $this->apiKey]);

        $response = $this->curl->post($url);

        return json_decode($response, true);
    }

    private function buildQueryString(array $params)
    {
        $query_array = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $query_array = array_merge($query_array, array_map(function ($v) use ($key) {
                    return urlencode($key) . '=' . urlencode($v);
                }, $value));
            } else {
                $query_array[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        return implode('&', $query_array);
    }

    private function getApiUrl()
    {
        return $this->apiUrl;
    }



    private function generateSign(string $queryString)
    {
        return hash_hmac('sha256', $queryString, $this->apiSecret);
    }

}