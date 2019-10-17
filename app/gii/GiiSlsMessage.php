<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsOrg;
use Yii;

/**
 * This is the model class for table "sls_message".
 *
 * @property int $id
 * @property int $org_fk
 * @property int $user_fk
 * @property string $ts_create
 * @property string $ts_update
 * @property string $message
 *
 * @property AnxUser $userFk
 * @property SlsOrg $orgFk
 */
class GiiSlsMessage extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['org_fk', 'user_fk', 'message'], 'required'],
            [['org_fk', 'user_fk'], 'integer'],
            [['ts_create', 'ts_update'], 'safe'],
            [['message'], 'string'],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['org_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsOrg::className(), 'targetAttribute' => ['org_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'org_fk' => 'Org Fk',
            'user_fk' => 'User Fk',
            'ts_create' => 'Ts Create',
            'ts_update' => 'Ts Update',
            'message' => 'Message',
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
    public function getOrgFk()
    {
        return $this->hasOne(SlsOrg::className(), ['id' => 'org_fk']);
    }
}
