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
 * @property string $name
 * @property string $comment
 * @property string $location
 * @property string $plan_summ
 * @property string $status
 *
 * @property AnxUser[] $anxUsers
 * @property SlsClient[] $slsClients
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
            [['name', 'location', 'plan_summ'], 'required'],
            [['comment', 'plan_summ'], 'string'],
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
            'name' => 'Name',
            'comment' => 'Comment',
            'location' => 'Location',
            'plan_summ' => 'Plan Summ',
            'status' => 'Status',
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
