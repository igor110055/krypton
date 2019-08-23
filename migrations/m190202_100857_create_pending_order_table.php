<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%pending_order}}`.
 */
class m190202_100857_create_pending_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('pending_order', [
            'id' => $this->primaryKey(),
            'market' => $this->string()->notNull(),
            'quantity' => $this->float()->notNull(),
            'price' => $this->float()->notNull(),
            'value' => $this->float()->notNull(),
            'condition' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'stop_loss' => $this->float(),
            'take_profit' => $this->float(),
            'uuid' => $this->string(),
            'modified' => $this->timestamp(),
            'crdate' => $this->timestamp(),
            'transaction_type' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('pending_order');
    }
}
