<?php

use yii\db\Migration;

/**
 * Handles adding staked to table `{{%portfolio_ticker}}`.
 */
class m210430_083420_add_staked_column_to_portfolio_ticker_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%portfolio_ticker}}', 'staked', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%portfolio_ticker}}', 'staked');
    }
}
