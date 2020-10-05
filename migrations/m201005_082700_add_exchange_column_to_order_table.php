<?php

use yii\db\Migration;

/**
 * Handles adding exchange to table `{{%order}}`.
 */
class m201005_082700_add_exchange_column_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'exchange', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'exchange');
    }
}
