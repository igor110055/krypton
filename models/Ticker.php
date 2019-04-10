<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ticker".
 *
 * @property int $id
 * @property string $exchange
 * @property string $symbol
 * @property int $timestamp
 * @property string $datetime
 * @property double $high
 * @property double $low
 * @property double $bid
 * @property double $ask
 * @property double $vwap
 * @property double $open
 * @property double $close
 * @property double $first
 * @property double $last
 * @property double $change
 * @property double $percentage
 * @property double $average
 * @property double $basevolume
 * @property double $quotevolume
 * @property string $created_at
 * @property string $updated_at
 */
class Ticker extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticker';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp'], 'integer'],
            [['datetime', 'created_at', 'updated_at'], 'safe'],
            [['high', 'low', 'bid', 'ask', 'vwap', 'open', 'close', 'first', 'last', 'change', 'percentage', 'average', 'basevolume', 'quotevolume'], 'number'],
            [['exchange', 'symbol'], 'string', 'max' => 255],
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
            'symbol' => 'Symbol',
            'timestamp' => 'Timestamp',
            'datetime' => 'Datetime',
            'high' => 'High',
            'low' => 'Low',
            'bid' => 'Bid',
            'ask' => 'Ask',
            'vwap' => 'Vwap',
            'open' => 'Open',
            'close' => 'Close',
            'first' => 'First',
            'last' => 'Last',
            'change' => 'Change',
            'percentage' => 'Percentage',
            'average' => 'Average',
            'basevolume' => 'Basevolume',
            'quotevolume' => 'Quotevolume',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
