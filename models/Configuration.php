<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuration".
 *
 * @property int $id
 * @property string $key
 * @property string $value
 */
class Configuration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    public function getValue($key)
    {
        $value = $this::findOne(['key' => $key]);

        return $value->value;
    }
}
