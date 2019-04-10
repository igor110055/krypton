<?php

namespace app\utils;

use Yii;

class ExchangeParser
{
    public static function safe_integer ($value) {
        return (isset ($value) && is_numeric ($value)) ? intval ($value) : null;
    }

    public static function safe_float ($value) {
        return (isset ($value) && is_numeric ($value)) ? floatval ($value) : null;
    }
}