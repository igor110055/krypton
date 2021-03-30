<?php

namespace app\models\Api;

use linslin\yii2\curl\Curl;

class CoinGecko
{
    private $curl;
    private $apiUrl = 'https://api.coingecko.com/api/v3/';

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function getTokensList()
    {
        $endPoint = 'coins/list';
        return \array_column($this->getResponse($endPoint), 'id', 'symbol');
    }

    public function getTokenPrices(array $tokenIds, $convertCurrency = 'usd')
    {
        $endPoint = 'simple/price';

        $params = [
            'ids' => implode(',', $tokenIds),
            'vs_currencies' => $convertCurrency
        ];

        return $this->getResponse($endPoint, $params);
    }

    private function getResponse(string $endPoint, $params = null): array
    {
        $url = $this->getApiUrl().$endPoint;

        if (is_array($params)) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->curl->get($url);

        return \json_decode($response, true);

    }

    private function getApiUrl()
    {
        return $this->apiUrl;
    }
}