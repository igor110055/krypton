<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%configuration}}`.
 */
class m190823_122447_create_configuration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%configuration}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(),
            'value' =>$this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%configuration}}');
    }
}
