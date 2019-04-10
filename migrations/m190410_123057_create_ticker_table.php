<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticker}}`.
 */
class m190410_123057_create_ticker_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticker}}', [
            'id' => $this->primaryKey(),
            'exchange' => $this->string()->null(),
            'symbol' => $this->string()->null(),
            'timestamp' => $this->bigInteger()->null(),
            'datetime' => $this->dateTime()->null(),
            'high' => $this->float()->null(),
            'low' => $this->float()->null(),
            'bid' => $this->float()->null(),
            'ask' => $this->float()->null(),
            'vwap' => $this->float()->null(),
            'open' => $this->float()->null(),
            'close' => $this->float()->null(),
            'first' => $this->float()->null(),
            'last' => $this->float()->null(),
            'change' => $this->float()->null(),
            'percentage' => $this->float()->null(),
            'average' => $this->float()->null(),
            'basevolume' => $this->float()->null(),
            'quotevolume' => $this->float()->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null()
        ]);

        $this->createIndex(
            'exchange_idx',
            'ticker',
            'exchange'
        );

        $this->createIndex(
            'symbol_idx',
            'ticker',
            'symbol'
        );

        $this->createIndex(
            'timestamp_idx',
            'ticker',
            'timestamp'
        );

        $this->createIndex(
            'datetime_idx',
            'ticker',
            'datetime'
        );
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ticker}}');
    }
}
