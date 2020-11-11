<?php

namespace app\interfaces;


interface ExchangeInterface
{
    public function getMarketsFormatted(): array;
    public function getTickerFormatted(string $market): array;
    public function getPricesFormatted(): array;
    public function getCurrentPrice(string $market): array;

}