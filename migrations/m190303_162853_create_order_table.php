<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m190303_162853_create_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull(),
            'market' => $this->string()->notNull(),
            'quantity' => $this->float()->notNull(),
            'price' => $this->float()->notNull(),
            'value' => $this->float(),
            'type' => $this->string()->notNull(),
            'stop_loss' => $this->float(),
            'take_profit' => $this->float(),
            'status' => $this->string(),
            'crdate' => $this->timestamp()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order}}');
    }
}
