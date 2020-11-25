<?php

namespace app\models;

use Yii;
use app\models\Api\Bittrex;
use app\utils\BittrexParser;

/**
 * This is the model class for table "pending_order".
 *
 * @property int $id
 * @property string $exchange
 * @property string $market
 * @property double $quantity
 * @property double $price
 * @property int $type
 * @property int $stop_loss
 * @property int $take_profit
 * @property double $last_bid
 * @property string $transaction_type
 */
class PendingOrder extends \yii\db\ActiveRecord
{
    const COND_MORE = 'More than price';
    const COND_LESS = 'Less than price';

    const TRANSACTION_STRICT = 'strict';
    const TRANSACTION_BEST = 'best';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pending_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exchange', 'market', 'quantity', 'price', 'type', 'condition'], 'required'],
            [['quantity', 'price', 'value'], 'number'],
            [['stop_loss', 'take_profit'], 'number'],
            [['market', 'exchange', 'condition', 'type', 'uuid', 'transaction_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exchange' => 'Exchange',
            'market' => 'Market',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'value' => 'Value',
            'type' => 'Type',
            'condition' => 'Condition',
            'stop_loss' => 'Stop Loss',
            'take_profit' => 'Take Profit',
            'uuid' => 'UUID',
            'transaction_type' => 'Transaction type'
        ];
    }

    public function beforeSave($insert)
    {
        $this->crdate = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    public function getMarketList()
    {
        $marketList = [];

        if ($this->exchange) {
            $api = Yii::createObject(['class' => 'app\models\Api\\' . $this->exchange]);
            $marketList = $api->getMarketsFormatted();
        }

        return $marketList;
    }
}
