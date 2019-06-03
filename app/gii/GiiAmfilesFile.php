<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "amfiles_file".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $user_fk
 * @property int $dir_fk
 * @property string $name
 * @property string $shared_key
 * @property string $file_name
 * @property int $size
 * @property string $ts_move
 *
 * @property AmfilesDirectory $dirFk
 * @property AnxUser $userFk
 */
class GiiAmfilesFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amfiles_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_move'], 'safe'],
            [['user_fk', 'dir_fk', 'name', 'file_name', 'size'], 'required'],
            [['user_fk', 'dir_fk', 'size'], 'integer'],
            [['name', 'file_name'], 'string', 'max' => 100],
            [['shared_key'], 'string', 'max' => 16],
            [['dir_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AmfilesDirectory::className(), 'targetAttribute' => ['dir_fk' => 'id']],
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
            'dir_fk' => 'Dir Fk',
            'name' => 'Name',
            'shared_key' => 'Shared Key',
            'file_name' => 'File Name',
            'size' => 'Size',
            'ts_move' => 'Ts Move',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirFk()
    {
        return $this->hasOne(AmfilesDirectory::className(), ['id' => 'dir_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }
}
