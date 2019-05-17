<?php
namespace app\models\Predictor;

use app\models\Predictor\DataHandler;
use Yii;

class Indicator
{
    private $dataHandler;

    public function __construct()
    {
        $dataHandler = new DataHandler();
        $this->dataHandler = $dataHandler;
    }

    /**
     * @param string $pair
     * @param null   $data
     * @param int    $period
     *
     * @return int
     *
     *          Average Directional Movement Index
     *
     *      TODO, this one needs more research for the returns
     *      http://www.investopedia.com/terms/a/adx.asp
     *
     * The ADX calculates the potential strength of a trend.
     * It fluctuates from 0 to 100, with readings below 20 indicating a weak trend and readings above 50 signaling a strong trend.
     * ADX can be used as confirmation whether the pair could possibly continue in its current trend or not.
     * ADX can also be used to determine when one should close a trade early. For instance, when ADX starts to slide below 50,
     * it indicates that the current trend is possibly losing steam.
     */
    public function adx($pair, $data=null, $period = 14)
    {
        if (empty($data)) {
            $data = $this->dataHandler->getRecentData($pair);
        }
        $adx = trader_adx($data['high'], $data['low'], $data['close'], $period);
        if (empty($adx)) {
            return -9;
        }
        $adx = array_pop($adx); #[count($adx) - 1];
        if ($adx > 50) {
            return -1; // overbought (sell)
        } elseif ($adx < 20) {
            return 1;  // underbought / oversold (buy)
        } else {
            return 0;
        }
    }

    /**
     * @param string $pair
     * @param null   $data
     * @param int    $period
     *
     * @return int
     *
     *      Commodity Channel Index
     */
    public function cci($pair, $data = null, $period = 14)
    {
        if (empty($data)) {
            $data = $this->dataHandler->getRecentData($pair);
        }
        # array $high , array $low , array $close [, integer $timePeriod ]
        $cci = trader_cci($data['high'], $data['low'], $data['close'], $period);
        $cci = array_pop($cci); #[count($cci) - 1];
        return $cci;
        if ($cci > 100) {
            return -1; // overbought
        } elseif ($cci < -100) {
            return 1;  // underbought
        } else {
            return 0;
        }

    }

    /**
     * @param string $pair
     * @param null   $data
     * @param int    $period
     *
     * @return int
     *
     *      Chande Momentum Oscillator
     */
    public function cmo($pair, $data = null, $period = 14)
    {
        if (empty($data)) {
            $data = $this->dataHandler->getRecentData($pair);
        }
        $cmo = trader_cmo($data['close'], $period);
        $cmo = array_pop($cmo); #[count($cmo) - 1];
        return $cmo;
        if ($cmo > 50) {
            return -1; // overbought
        } elseif ($cmo < -50) {
            return 1;  // underbought
        } else {
            return 0;
        }
    }

    /**
     * @param string $pair
     * @param null   $data
     * @param int    $period
     *
     * @return int
     *
     *      Money flow index
     */
    public function mfi($pair, $data = null, $period = 14)
    {
        if (empty($data)) {
            $data = $this->dataHandler->getRecentData($pair);
        }
        $mfi = trader_mfi($data['high'], $data['low'], $data['close'], $data['volume'], $period);
        $mfi = array_pop($mfi); #[count($mfi) - 1];
        return $mfi;
        if ($mfi > 80) {
            return -1; // overbought
        } elseif ($mfi < 10) {
            return 1;  // underbought
        } else {
            return 0;
        }
    }

    //rsi


}