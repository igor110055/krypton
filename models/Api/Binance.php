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

    public function getTickerFormatted(string $market): array
    {
        $ticker = $this->getTicker24($market);

        return BinanceParser::parseTickerForPendingOrder($ticker);

    }

}