<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%portfolio_ticker}}`.
 */
class m201125_133454_create_portfolio_ticker_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%portfolio_ticker}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->dateTime(),
            'hodl_btc_value' => $this->float(),
            'exchange_btc_value' => $this->float(),
            'hodl_percent' => $this->float(),
            'btc_price' => $this->float(),
            'usd_price' => $this->float(),
            'deposit' => $this->float(),
            'pln_diff' => $this->float(),
            'change' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%portfolio_ticker}}');
    }
}
