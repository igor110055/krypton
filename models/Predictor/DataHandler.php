<?php
namespace app\models\Predictor;

use Yii;

class DataHandler
{
    /**
     * Get data from tocker table. Pair example: BTC-LTC
     *
     * @param string $pair
     * @param int    $limit
     * @param bool   $day_data
     * @param int    $hour
     * @param string $periodSize
     * @param bool   $returnRS
     *
     * @return array
     */
    public function getRecentData($pair, $periodSize='1m', $limit=168, $returnRS=false)
    {
        /**
         *  we need to cache this as many strategies will be
         *  doing identical pulls for signals.
         */

        $connection_name = '';

//        $key = 'recent::'.$pair.'::'.$limit."::$day_data::$hour::$periodSize::$connection_name";
//        if(\Cache::has($key)) {
//            return \Cache::get($key);
//        }

        $connection = Yii::$app->getDb();

        $exchange = 'BINANCE';

        $timeslice = 60;
        switch($periodSize) {
            case '1m':
                $timescale = '1 minute';
                $timeslice = 60;
                break;
            case '5m':
                $timescale = '5 minutes';
                $timeslice = 300;
                break;
            case '10m':
                $timescale = '10 minutes';
                $timeslice = 600;
                break;
            case '15m':
                $timescale = '15 minutes';
                $timeslice = 900;
                break;
            case '30m':
                $timescale = '30 minutes';
                $timeslice = 1800;
                break;
            case '1h':
                $timescale = '1 hour';
                $timeslice = 3600;
                break;
            case '4h':
                $timescale = '4 hours';
                $timeslice = 14400;
                break;
            case '1d':
                $timescale = '1 day';
                $timeslice = 86400;
                break;
            case '1w':
                $timescale = '1 week';
                $timeslice = 604800;
                break;
        }
        $current_time = time();
        $offset = ($current_time - ($timeslice * $limit)) -1;

        $sql = "
          SELECT 
            exchange,
            SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY created_at), ',', 1 ) AS `open`,
            SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY bid DESC), ',', 1 ) AS `high`,
            SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY bid), ',', 1 ) AS `low`,
            SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY created_at DESC), ',', 1 ) AS `close`,
            SUM(basevolume) AS volume,
            ROUND((CEILING(UNIX_TIMESTAMP(`created_at`) / $timeslice) * $timeslice)) AS buckettime,
            round(AVG(bid),11) AS avgbid,
            round(AVG(ask),11) AS avgask,
            AVG(baseVolume) AS avgvolume
          FROM ticker
          WHERE symbol = '$pair'
          AND UNIX_TIMESTAMP(`created_at`) > ($offset)
          AND exchange = '$exchange'
          GROUP BY buckettime 
          ORDER BY buckettime DESC";

        $command = $connection->createCommand($sql);
        $results = $command->queryAll();

        if (!$returnRS) {
            $results = $this->organizePairData($results, $limit);
        }

//        \Cache::put($key, $ret, 2);
        return $results;
    }

    /**
     * @param $datas
     *
     * @return array
     */
    public function organizePairData($datas, $limit)
    {
        $ret = array();
        foreach ($datas as $data) {
            $ret[$data['exchange']]['timestamp'][]   = $data['buckettime'];
            $ret[$data['exchange']]['date'][]   = gmdate("j-M-y", $data['buckettime']);
            $ret[$data['exchange']]['low'][]    = $data['low'];
            $ret[$data['exchange']]['high'][]   = $data['high'];
            $ret[$data['exchange']]['open'][]   = $data['open'];
            $ret[$data['exchange']]['close'][]  = $data['close'];
            $ret[$data['exchange']]['volume'][] = $data['volume'];
        }
        foreach($ret as $ex => $opt) {
            foreach ($opt as $key => $rettemmp) {
                $ret[$ex][$key] = array_reverse($rettemmp);
                $ret[$ex][$key] = array_slice($ret[$ex][$key], 0, $limit, true);
            }
        }
        return $ret['BINANCE'];
    }
}