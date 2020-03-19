<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "log_error".
 *
 * @property int $id
 * @property string $ts_create
 * @property string|null $props
 */
class GiiLogError extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_error';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['props'], 'string'],
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
            'props' => 'Props',
        ];
    }
}
