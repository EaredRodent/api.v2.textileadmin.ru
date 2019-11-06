<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsMessage;
use app\modules\v1\models\sls\SlsOrg;
use Yii;

/**
 * This is the model class for table "sls_message_state".
 *
 * @property int $id
 * @property int $user_fk
 * @property int $org_fk
 * @property int $last_message_fk
 *
 * @property AnxUser $userFk
 * @property SlsMessage $lastMessageFk
 * @property SlsOrg $orgFk
 */
class GiiSlsMessageState extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_message_state';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_fk', 'org_fk', 'last_message_fk'], 'required'],
            [['user_fk', 'org_fk', 'last_message_fk'], 'integer'],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['last_message_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsMessage::className(), 'targetAttribute' => ['last_message_fk' => 'id']],
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
            'user_fk' => 'User Fk',
            'org_fk' => 'Org Fk',
            'last_message_fk' => 'Last Message Fk',
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
    public function getLastMessageFk()
    {
        return $this->hasOne(SlsMessage::className(), ['id' => 'last_message_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrgFk()
    {
        return $this->hasOne(SlsOrg::className(), ['id' => 'org_fk']);
    }
}
