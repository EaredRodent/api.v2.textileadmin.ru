<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "amfiles_directory".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $user_fk
 * @property string $type
 * @property string $name
 *
 * @property AnxUser $userFk
 * @property AmfilesFile[] $amfilesFiles
 */
class GiiAmfilesDirectory extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amfiles_directory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['user_fk'], 'integer'],
            [['type', 'name'], 'required'],
            [['type'], 'string'],
            [['name'], 'string', 'max' => 100],
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
            'type' => 'Type',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAmfilesFiles()
    {
        return $this->hasMany(AmfilesFile::className(), ['dir_fk' => 'id']);
    }
}
