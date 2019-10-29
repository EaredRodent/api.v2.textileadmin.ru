<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsClient;
use Yii;

/**
 * This is the model class for table "sls_org".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $ts_accept
 * @property string $ts_del
 * @property string $name
 * @property string $comment
 * @property string $location
 * @property string $plan_summ
 * @property int $status
 * @property string $state
 * @property int $manager_fk ссылка на менеджера
 * @property string $discount
 *
 * @property AnxUser[] $anxUsers
 * @property SlsClient[] $slsClients
 * @property SlsMessage[] $slsMessages
 * @property AnxUser $managerFk
 */
class GiiSlsOrg extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_org';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_accept', 'ts_del'], 'safe'],
            [['name', 'location', 'plan_summ', 'state'], 'required'],
            [['comment', 'plan_summ', 'state'], 'string'],
            [['status', 'manager_fk'], 'integer'],
            [['discount'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['location'], 'string', 'max' => 255],
            [['manager_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['manager_fk' => 'id']],
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
            'ts_accept' => 'Ts Accept',
            'ts_del' => 'Ts Del',
            'name' => 'Name',
            'comment' => 'Comment',
            'location' => 'Location',
            'plan_summ' => 'Plan Summ',
            'status' => 'Status',
            'state' => 'State',
            'manager_fk' => 'Manager Fk',
            'discount' => 'Discount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnxUsers()
    {
        return $this->hasMany(AnxUser::className(), ['org_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsClients()
    {
        return $this->hasMany(SlsClient::className(), ['org_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMessages()
    {
        return $this->hasMany(SlsMessage::className(), ['org_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagerFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'manager_fk']);
    }
}
