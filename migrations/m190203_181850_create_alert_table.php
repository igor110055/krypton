<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%alert}}`.
 */
class m190203_181850_create_alert_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alert}}', [
            'id' => $this->primaryKey(),
            'market' => $this->string()->notNull(),
            'price' => $this->double()->notNull(),
            'condition' => $this->string()->notNull(),
            'message' => $this->text(),
            'modified' => $this->timestamp(),
            'crdate' => $this->timestamp()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alert}}');
    }
}
