<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_event".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $user_fk
 * @property string $event
 * @property string $params json
 *
 * @property AnxUser $userFk
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
            [['user_fk'], 'integer'],
            [['event'], 'required'],
            [['params'], 'string'],
            [['event'], 'string', 'max' => 255],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
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
            'user_fk' => 'User Fk',
            'event' => 'Event',
            'params' => 'Params',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }
}
