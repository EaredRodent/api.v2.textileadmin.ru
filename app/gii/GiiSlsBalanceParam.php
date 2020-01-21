<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "sls_balance_param".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property int $value
 * @property string $ts_update
 */
class GiiSlsBalanceParam extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_balance_param';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name', 'value'], 'required'],
            [['type'], 'string'],
            [['value'], 'integer'],
            [['ts_update'], 'safe'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'value' => 'Value',
            'ts_update' => 'Ts Update',
        ];
    }
}
