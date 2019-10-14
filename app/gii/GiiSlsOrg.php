<?php

namespace app\gii;

use app\models\AnxUser;
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
 * @property string $status
 * @property string $state
 * @property int $manager_fk ссылка на менеджера
 *
 * @property AnxUser[] $anxUsers
 * @property SlsClient[] $slsClients
 */
class GiiSlsOrg extends \yii\db\ActiveRecord
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
            [['manager_fk'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['location', 'status'], 'string', 'max' => 255],
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
}
