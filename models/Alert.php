<?php

namespace app\models;

use Yii;
use app\models\Api\Bittrex;
use app\utils\BittrexParser;

/**
 * This is the model class for table "alert".
 *
 * @property int $id
 * @property string $market
 * @property double $price
 * @property string $condition
 * @property string $message
 * @property boolean $is_active
 * @property string $modified
 * @property string $crdate
 */
class Alert extends \yii\db\ActiveRecord
{
    const COND_MORE = '>=';
    const COND_LESS = '<=';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alert';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['market', 'price', 'condition'], 'required'],
            [['price'], 'number'],
            [['is_active'], 'boolean'],
            [['message'], 'string'],
            [['modified', 'crdate'], 'safe'],
            [['market', 'condition'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'market' => 'Market',
            'price' => 'Price',
            'condition' => 'Condition',
            'message' => 'Message',
            'is_active' => 'Is active',
            'modified' => 'Modified',
            'crdate' => 'Crdate',
        ];
    }

    public function getMarketList()
    {
        $bittrexApi = new Bittrex();
        $bittrexCacher = new EndPointCacher($bittrexApi);

        $marketJson = $bittrexCacher->getMartkets();
        $marketList = BittrexParser::getMarketList($marketJson);

        return $marketList;
    }

    public function beforeSave($insert)
    {
        $this->crdate = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}
