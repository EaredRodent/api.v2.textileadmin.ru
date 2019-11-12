<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_event".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $event
 * @property string $params json
 */
class GiiLogEvent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['event'], 'required'],
            [['params'], 'string'],
            [['event'], 'string', 'max' => 255],
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
            'event' => 'Event',
            'params' => 'Params',
        ];
    }
}
