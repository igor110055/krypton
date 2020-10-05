<?php

use yii\db\Migration;

/**
 * Handles adding exchange to table `{{%pending_order}}`.
 */
class m201005_090008_add_exchange_column_to_pending_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pending_order}}', 'exchange', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%pending_order}}', 'exchange');
    }
}
