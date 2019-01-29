<?php
namespace app\models;

use Yii;
use linslin\yii2\curl;

class Api
{
    private $apiUrl = 'https://api.bittrex.com/api/v1.1/';
    private $apiSecret;
    private $apiKey;
    private $curl;

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

    public function getOpenOrders()
    {
        $endPoint = 'market/getopenorders';


        return $this->getResponse($endPoint, null, true);
    }

    protected function getResponse($endPoint, $params, $auth = false)
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

        $hash = $this->getMessageHash($url);

        $this->curl->setHeader('apisign', $hash);
        $response = $this->curl->get($url);

        return $response;

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

}