<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "portfolio_ticker".
 *
 * @property int $id
 * @property string $created_at
 * @property double $hodl_btc_value
 * @property double $exchange_btc_value
 * @property double $hodl_percent
 * @property double $btc_price
 * @property double $usd_price
 * @property double $deposit
 * @property double $pln_diff
 * @property double $change
 */
class PortfolioTicker extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'portfolio_ticker';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['hodl_btc_value', 'exchange_btc_value', 'hodl_percent', 'btc_price', 'usd_price', 'deposit', 'pln_diff', 'change'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'hodl_btc_value' => 'Hodl Btc Value',
            'exchange_btc_value' => 'Exchange Btc Value',
            'hodl_percent' => 'Hodl Percent',
            'btc_price' => 'Btc Price',
            'usd_price' => 'Usd Price',
            'deposit' => 'Deposit',
            'pln_diff' => 'Pln Diff',
            'change' => 'Change',
        ];
    }
}
