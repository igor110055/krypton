<?php

use yii\db\Migration;

/**
 * Handles adding portfolio to table `{{%hodl_position}}`.
 */
class m210430_125224_add_portfolio_column_to_hodl_position_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%hodl_position}}', 'portfolio_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%hodl_position}}', 'portfolio_id');
    }
}
