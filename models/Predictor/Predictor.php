<?php
namespace app\models\Predictor;

use Yii;
use app\models\Predictor\Indicator;
use app\models\Predictor\DataHandler;

class Predictor
{
    private $activeStrategy;
    private $markets;
    private $dataHandler;
    private $indicator;

    public function __construct()
    {
        $this->markets = Yii::$app->params['markets'];
        $this->indicator = new Indicator();
        $this->dataHandler = new DataHandler();
    }

    public function checkCurrencies()
    {
        $results = [];
//        $time_start = microtime(true);

        foreach ($this->markets as $market) {

            $recentData = $this->dataHandler->getRecentData($market, '1h');

            $cci = $this->indicator->cci($market, $recentData);
            $cmo = $this->indicator->cmo($market, $recentData);
            $mfi = $this->indicator->mfi($market, $recentData);

            $result = [
                'market' => $market,
                'cci' => $cci,
                'cmo' => $cmo,
                'mfi' => $mfi
            ];

            $results[] = $result;
        }
//        $time_end = microtime(true);
//        $execution_time = ($time_end - $time_start);
//        var_dump($results, $execution_time);
//        exit;
        return $results;
    }
}