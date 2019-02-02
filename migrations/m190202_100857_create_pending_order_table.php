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
            'quantity' => $this->double()->notNull(),
            'price' => $this->double()->notNull(),
            'type' => $this->integer()->notNull(),
            'stop_loss' => $this->integer(),
            'start_earn' => $this->integer(),
            'last_bid' => $this->double()
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
