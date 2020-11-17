<?php
namespace app\models\Api;

use app\interfaces\ExchangeInterface;
use app\utils\BittrexParser;
use Yii;
use linslin\yii2\curl;

class Bittrex implements ExchangeInterface
{
    private $apiUrl = 'https://api.bittrex.com/api/v1.1/';
    private $apiSecret;
    private $apiKey;
    private $curl;
    private $cachePath = 'datasource/api/bittrex/';

    private $dataSource = [
        'markets' => 'markets.json'
    ];

    public function __construct()
    {
        $this->apiKey = Yii::$app->params['bittrex']['apiKey'];
        $this->apiSecret = Yii::$app->params['bittrex']['secret'];
        $this->curl = new curl\Curl();
    }

    public function getMarkets()
    {
        $endPoint = 'public/getmarkets';

        return $this->getResponse($endPoint);
    }

    public function getCurrencies()
    {
        $endPoint = 'public/getcurrencies';

        return $this->getResponse($endPoint);
    }

    public function getTicker($market)
    {
        $endPoint = 'public/getticker';
        $params = null;

        if ($market) {
            $params = [
                'market' => $market
            ];
        }

        return $this->getResponse($endPoint, $params);
    }

    public function getTickerFormatted(string $market): array
    {
        $ticker = $this->getTicker($market);

        return $ticker['result'];
    }

    public function getMarketSummaries()
    {
        $endPoint = 'public/getmarketsummaries';

        return $this->getResponse($endPoint);
    }

    public function getMarketSummary($market)
    {
        $endPoint = 'public/getmarketsummary';
        $params = null;

        if ($market) {
            $params = [
                'market' => $market
            ];
        }

        return $this->getResponse($endPoint, $params);
    }

    public function getOrderBook($market, $type = 'both')
    {
        $endPoint = 'public/getorderbook';
        $params = null;

        if ($market && $type) {
            $params = [
                'market' => $market,
                'type' => $type
            ];
        }

        return $this->getResponse($endPoint, $params);
    }

    public function getMarketHistory($market)
    {
        $endPoint = 'public/getmarkethistory';
        $params = null;

        if ($market) {
            $params = [
                'market' => $market
            ];
        }

        return $this->getResponse($endPoint, $params);
    }

    public function getOpenOrders($market = null)
    {
        $endPoint = 'market/getopenorders';
        $params = null;

        if ($market) {
            $params = [
                'market' => $market
            ];
        }

        return $this->getResponse($endPoint, $params, true);
    }

    public function placeBuyOrder($market, $quantity, $rate)
    {
        $endPoint = 'market/buylimit';
        $params = null;

        if ($market && $quantity && $rate) {
            $params = [
                'market' => $market,
                'quantity' => $quantity,
                'rate' => $rate
            ];
        }

        $result = $this->getResponse($endPoint, $params, true);

        if ($result['success']) {
            return [
                'success' => true,
                'orderId' => $result['result']['uuid']
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    public function placeSellOrder($market, $quantity, $rate)
    {
        $endPoint = 'market/selllimit';
        $params = null;

        if ($market && $quantity && $rate) {
            $params = [
                'market' => $market,
                'quantity' => $quantity,
                'rate' => $rate
            ];
        }

        $result = $this->getResponse($endPoint, $params, true);
        if ($result['success']) {
            return [
                'success' => true,
                'orderId' => $result['result']['uuid']
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    public function cancelOrder($uuid)
    {
        $endPoint = 'market/cancel';
        $params = null;

        if ($uuid) {
            $params['uuid'] = $uuid;
        }

        $response = $this->getResponse($endPoint, $params, true);
        return $response;
    }

    public function getBalances()
    {
        $endPoint = 'account/getbalances';

        return $this->getResponse($endPoint, null, true);
    }

    public function getBalance($currency)
    {
        $endPoint = 'account/getbalance';
        $params = null;

        if ($currency) {
            $params = [
                'currency' => $currency
            ];
        }

        return $this->getResponse($endPoint, $params, true);
    }

    public function getDepositAddress($currency)
    {
        $endPoint = 'account/getdepositaddress';
        $params = null;

        if ($currency) {
            $params = [
                'currency' => $currency
            ];
        }

        return $this->getResponse($endPoint, $params, true);
    }

    public function withdraw($currency, $quantity, $address, $paymentId = null)
    {
        $endPoint = 'account/withdraw';
        $params = null;

        if ($currency && $quantity && $address) {
            $params = [
                'currency' => $currency,
                'quantity' => $quantity,
                'address' => $address
            ];
        }
        if ($paymentId) {
            $params['paymentid'] = $paymentId;
        }

        $response = $this->getResponse($endPoint, $params, true);

        return $response;
    }

    public function getOrder($uuid)
    {
        $endPoint = 'account/getorder';
        $params = null;

        if ($uuid) {
            $params['uuid'] = $uuid;
        }

        $response = $this->getResponse($endPoint, $params, true);

        return $response;
    }

    public function getOrders($market = null)
    {
        $endPoint = 'account/getorderhistory';
        $params = null;

        if ($market) {
            $params['market'] = $market;
        }

        $response = $this->getResponse($endPoint, $params, true);

        return $response;
    }

    public function getWithdrawalHistory()
    {
        $endPoint = 'account/getwithdrawalhistory';

        $response = $this->getResponse($endPoint, null, true);

        return $response;
    }

    public function getDepositHistory()
    {
        $endPoint = 'account/getdeposithistory';

        $response = $this->getResponse($endPoint, null, true);

        return $response;
    }

    protected function getResponse($endPoint, $params = null, $auth = false)
    {
        $authParams = [
            'apikey' => $this->apiKey,
            'nonce' => time()
        ];

        $url = $this->getApiUrl().$endPoint;

        if ($auth) {
            if (is_array($params)) {
                $params = array_merge($params, $authParams);
            } else {
                $params = $authParams;
            }
        }
        if (is_array($params)) {
            $url .= '?'.http_build_query($params);
        }
        if ($auth) {
            $hash = $this->getMessageHash($url);
            $this->curl->setHeader('apisign', $hash);
        }
        $response = $this->curl->get($url);

        return json_decode($response, true);

    }

    public function getActualPrices()
    {
        $marketSummaries = $this->getMarketSummaries();
        if (!$marketSummaries['success']) {
            return false;
        }
        $prices = BittrexParser::getPricesFromSummaries($marketSummaries);

        return $prices;
    }

    public function getMarketSymbols()
    {
        $bittrexMarkets = $this->getMarkets()['result'];
        if (!$bittrexMarkets['success']) {
            return false;
        }
        $marketSymbols = [];
        foreach ($bittrexMarkets as $bitrexMarket) {
            if (strstr($bitrexMarket['MarketName'], 'BTC-')) {
                $marketSymbols[] = $bitrexMarket['MarketName'];
            }
        }
        return $marketSymbols;

    }
    protected function getMessageHash($message)
    {
        $hash = hash_hmac('sha512', $message, $this->getApiSecret());

        return $hash;
    }

    protected function getApiUrl()
    {
        return $this->apiUrl;
    }

    protected function getApiSecret()
    {
        return $this->apiSecret;
    }

    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function getDataSource($key)
    {
        if (isset($this->dataSource[$key])) {
            return $this->dataSource[$key];
        }
        return $this->dataSource;
    }

    public function getMarketsFormatted(): array
    {
        $marketJson = $this->getMarkets();
        $marketList = BittrexParser::getMarketList($marketJson);

        return $marketList;
    }

    public function getPricesFormatted(): array
    {
        $marketSummaries = $this->getMarketSummaries();

        $pricesFormatted = BittrexParser::getPricesFromSummaries($marketSummaries);

        return $pricesFormatted;
    }

    public function getCurrentPrice(string $market): array
    {
        $ticker = $this->getTicker($market);

        $result = [
            'ask' => $ticker['result']['Ask'],
            'bid' => $ticker['result']['Bid'],
            'last' => $ticker['result']['Last'],
        ];

        return $result;
    }

}