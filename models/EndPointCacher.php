<?php
namespace app\models;

use Yii;

class EndPointCacher
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function getMartkets()
    {
        $path = Yii::$app->basePath.'/'.$this->api->getCachePath().$this->api->getDataSource('markets');

        if (file_exists($path)) {
            return file_get_contents($path);
        }
    }

    public function downloadMarkets()
    {
        $markets = $this->api->getMarkets();
        $savePath = $this->api->getCachePath();
        $fileName = $this->api->getDataSource('markets');

        $path = Yii::$app->basePath.'/'.$savePath.$fileName;

        file_put_contents($path, $markets);
    }

}