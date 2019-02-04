<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alert".
 *
 * @property int $id
 * @property string $market
 * @property double $price
 * @property string $condition
 * @property string $message
 * @property string $modified
 * @property string $crdate
 */
class Alert extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alert';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['market', 'price', 'condition'], 'required'],
            [['price'], 'number'],
            [['message'], 'string'],
            [['modified', 'crdate'], 'safe'],
            [['market', 'condition'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'market' => 'Market',
            'price' => 'Price',
            'condition' => 'Condition',
            'message' => 'Message',
            'modified' => 'Modified',
            'crdate' => 'Crdate',
        ];
    }
}
