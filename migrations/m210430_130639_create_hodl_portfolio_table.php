<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hodl_portfolio}}`.
 */
class m210430_130639_create_hodl_portfolio_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hodl_portfolio}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%hodl_portfolio}}');
    }
}
