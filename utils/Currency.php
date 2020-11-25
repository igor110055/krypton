<?php

namespace app\utils;


class Currency
{
    public static function getUsdToPlnRate(): float
    {
        $endPoint = 'http://api.nbp.pl/api/exchangerates/rates/a/usd/?format=json';
        $result = file_get_contents($endPoint);
        $usdData = json_decode($result, true);

        return (float)$usdData['rates'][0]['mid'];
    }
}