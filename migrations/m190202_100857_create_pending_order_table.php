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
            'condition' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'stop_loss' => $this->float(),
            'start_earn' => $this->float(),
            'last_bid' => $this->float(),
            'modified' => $this->timestamp(),
            'crdate' => $this->timestamp()
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
