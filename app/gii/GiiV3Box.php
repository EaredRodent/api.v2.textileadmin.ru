<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "v3_box".
 *
 * @property int $id
 * @property int $user_fk
 * @property string $name
 *
 * @property AnxUser $userFk
 * @property V3MoneyEvent[] $v3MoneyEvents
 * @property V3MoneyEvent[] $v3MoneyEvents0
 */
class GiiV3Box extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v3_box';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_fk', 'name'], 'required'],
            [['user_fk'], 'integer'],
            [['name'], 'string', 'max' => 50],
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
            'user_fk' => 'User Fk',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3MoneyEvents()
    {
        return $this->hasMany(V3MoneyEvent::className(), ['box_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3MoneyEvents0()
    {
        return $this->hasMany(V3MoneyEvent::className(), ['trans_box_fk' => 'id']);
    }
}
