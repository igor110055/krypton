<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ohlcv}}`.
 */
class m190410_130244_create_ohlcv_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ohlcv}}', [
            'id' => $this->primaryKey(),
            'exchange' => $this->string()->null(),
            'symbol' => $this->string()->null(),
            'timestamp' => $this->bigInteger()->null(),
            'datetime' => $this->dateTime()->null(),
            'open' => $this->float()->null(),
            'high' => $this->float()->null(),
            'low' => $this->float()->null(),
            'close' => $this->float()->null(),
            'volume' => $this->float()->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null()
        ]);

        $this->createIndex(
            'datetime_ohlcv_idx',
            'ohlcv',
            'datetime'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ohlcv}}');
    }
}
