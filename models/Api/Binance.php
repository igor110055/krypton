<?php
namespace app\models\Api;

use Yii;
use linslin\yii2\curl;

class Binance
{
    private $apiUrl = 'https://api.binance.com/';
    private $cachePath = 'datasource/api/binance/';

    private $curl;

    public function __construct()
    {
        $this->curl = new curl\Curl();
    }

    public function getPrices()
    {
        $endPoint = 'api/v3/ticker/price';

        $response = $this->getResponse($endPoint);

        return $response;
    }

    protected function getResponse($endPoint, $params = null)
    {
        $url = $this->getApiUrl().$endPoint;

        if (is_array($params)) {
            $url .= '?'.http_build_query($params);
        }

        $response = $this->curl->get($url);

        return json_decode($response, true);

    }

    protected function getApiUrl()
    {
        return $this->apiUrl;
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
}