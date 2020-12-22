<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hodl_position}}`.
 */
class m201202_120927_create_hodl_position_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hodl_position}}', [
            'id' => $this->primaryKey(),
            'buy_date' => $this->dateTime(),
            'sell_date' => $this->dateTime(),
            'market' => $this->string(),
            'quantity' => $this->float(),
            'buy_price' => $this->float(),
            'sell_price' => $this->float(),
            'buy_value' => $this->float(),
            'sell_value' => $this->float(),
            'status' => $this->string(),
            'val_diff' => $this->float(),
            'price_diff' => $this->float(),
            'pln_value' => $this->float(),
            'pln_buy_value' => $this->float(),
            'pln_diff_value' => $this->float(),
            'comment' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%hodl_position}}');
    }
}
