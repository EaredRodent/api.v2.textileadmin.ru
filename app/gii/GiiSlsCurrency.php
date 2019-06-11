<?php

namespace app\gii;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sls_currency".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $date
 * @property string $value
 * @property string $unit
 */
class GiiSlsCurrency extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'date'], 'safe'],
            [['value'], 'number'],
            [['unit'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ts_create' => 'Ts Create',
            'date' => 'Date',
            'value' => 'Value',
            'unit' => 'Unit',
        ];
    }
}
