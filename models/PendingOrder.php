<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pending_order".
 *
 * @property int $id
 * @property string $market
 * @property double $quantity
 * @property double $price
 * @property int $type
 * @property int $stop_loss
 * @property int $start_earn
 * @property double $last_bid
 */
class PendingOrder extends \yii\db\ActiveRecord
{
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
            [['market', 'quantity', 'price', 'type'], 'required'],
            [['quantity', 'price', 'last_bid'], 'number'],
            [['type', 'stop_loss', 'start_earn'], 'integer'],
            [['market'], 'string', 'max' => 255],
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
            'quantity' => 'Quantity',
            'price' => 'Price',
            'type' => 'Type',
            'stop_loss' => 'Stop Loss',
            'start_earn' => 'Start Earn',
            'last_bid' => 'Last Bid',
        ];
    }
}
