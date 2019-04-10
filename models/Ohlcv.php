<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ohlcv".
 *
 * @property int $id
 * @property string $exchange
 * @property string $symbol
 * @property int $timestamp
 * @property string $datetime
 * @property double $open
 * @property double $high
 * @property double $low
 * @property double $close
 * @property double $volume
 * @property string $created_at
 * @property string $updated_at
 */
class Ohlcv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ohlcv';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp'], 'integer'],
            [['datetime', 'created_at', 'updated_at'], 'safe'],
            [['open', 'high', 'low', 'close', 'volume'], 'number'],
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
            'open' => 'Open',
            'high' => 'High',
            'low' => 'Low',
            'close' => 'Close',
            'volume' => 'Volume',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
