<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hodl_position".
 *
 * @property int $id
 * @property string $buy_date
 * @property string $sell_date
 * @property string $market
 * @property double $quantity
 * @property double $buy_price
 * @property double $sell_price
 * @property double $buy_value
 * @property double $sell_value
 * @property string $status
 * @property double $val_diff
 * @property double $price_diff
 * @property double $pln_buy_value
 * @property double $pln_value
 * @property double $pln_diff_value
 * @property string $comment
 */
class HodlPosition extends \yii\db\ActiveRecord
{

    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE = 'done';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hodl_position';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['buy_date', 'sell_date'], 'safe'],
            [['quantity', 'buy_price', 'sell_price', 'buy_value', 'sell_value', 'val_diff', 'price_diff', 'pln_buy_value', 'pln_value', 'pln_diff_value'], 'number'],
            [['market', 'status', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buy_date' => 'Buy Date',
            'sell_date' => 'Sell Date',
            'market' => 'Market',
            'quantity' => 'Quantity',
            'buy_price' => 'Buy Price',
            'sell_price' => 'Sell Price',
            'buy_value' => 'Buy Value',
            'sell_value' => 'Sell Value',
            'status' => 'Status',
            'val_diff' => 'Val Diff',
            'price_diff' => '% change',
            'pln_buy_value' => 'Pln buy value',
            'pln_value' => 'Pln Value',
            'pln_diff_value' => 'Pln Diff Value',
            'comment' => 'Comment',
        ];
    }

    public static function getProcessingBTCvalueSum(array $currentPrices): float
    {
        $hodlPositions = HodlPosition::find()->where(['status' => HodlPosition::STATUS_PROCESSING])->all();
        $hodlBTCvalueSum = 0;
        foreach ($hodlPositions as $position) {
            if ($position->market == 'BTCUSDT') {
                $hodlBTCvalueSum += $position->quantity;
                continue;
            }
            $asset = str_replace('USDT', '', $position->market);
            $value = $position->quantity * $currentPrices['Binance'][$asset . 'BTC'];
            $hodlBTCvalueSum += $value;
        }

        return $hodlBTCvalueSum;
    }
}
