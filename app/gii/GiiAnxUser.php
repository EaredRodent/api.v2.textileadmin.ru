<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrg;
use app\modules\v1\models\sls\SlsPreorder;
use Yii;

/**
 * This is the model class for table "anx_user".
 *
 * @property int $id
 * @property string $project к какому проекту относится пользователь
 * @property string $login может быть email если project = b2b
 * @property string $name
 * @property string $role
 * @property int $status
 * @property string $phone
 * @property string $hash
 * @property string $auth_key
 * @property string $url_key
 * @property string $accesstoken
 * @property int $org_fk если юзер контактное лицо клиента b2b - то тут ссылка на свмого клиента
 *
 * @property AmfilesDirectory[] $amfilesDirectories
 * @property AmfilesFile[] $amfilesFiles
 * @property AnxCmdLog[] $anxCmdLogs
 * @property AnxDbLog[] $anxDbLogs
 * @property SlsOrg $orgFk
 * @property PrInventItem[] $prInventItems
 * @property PrTsdItem[] $prTsdItems
 * @property SlsClient[] $slsClients
 * @property SlsInvoice[] $slsInvoices
 * @property SlsMessage[] $slsMessages
 * @property SlsMessageState[] $slsMessageStates
 * @property SlsMoney[] $slsMoneys
 * @property SlsOrder[] $slsOrders
 * @property SlsOrg[] $slsOrgs
 * @property SlsPreorder[] $slsPreorders
 * @property SlsStatPrice[] $slsStatPrices
 */
class GiiAnxUser extends \app\modules\v1\classes\ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anx_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project'], 'string'],
            [['login', 'name', 'role', 'status', 'hash', 'auth_key'], 'required'],
            [['status', 'org_fk'], 'integer'],
            [['login', 'name', 'role', 'phone', 'auth_key'], 'string', 'max' => 45],
            [['hash'], 'string', 'max' => 60],
            [['url_key'], 'string', 'max' => 16],
            [['accesstoken'], 'string', 'max' => 128],
            [['login'], 'unique'],
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
            'project' => 'Project',
            'login' => 'Login',
            'name' => 'Name',
            'role' => 'Role',
            'status' => 'Status',
            'phone' => 'Phone',
            'hash' => 'Hash',
            'auth_key' => 'Auth Key',
            'url_key' => 'Url Key',
            'accesstoken' => 'Accesstoken',
            'org_fk' => 'Org Fk',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmfilesDirectories()
    {
        return $this->hasMany(AmfilesDirectory::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmfilesFiles()
    {
        return $this->hasMany(AmfilesFile::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnxCmdLogs()
    {
        return $this->hasMany(AnxCmdLog::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnxDbLogs()
    {
        return $this->hasMany(AnxDbLog::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrgFk()
    {
        return $this->hasOne(SlsOrg::className(), ['id' => 'org_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrInventItems()
    {
        return $this->hasMany(PrInventItem::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrTsdItems()
    {
        return $this->hasMany(PrTsdItem::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsClients()
    {
        return $this->hasMany(SlsClient::className(), ['manager_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsInvoices()
    {
        return $this->hasMany(SlsInvoice::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMessages()
    {
        return $this->hasMany(SlsMessage::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMessageStates()
    {
        return $this->hasMany(SlsMessageState::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsOrders()
    {
        return $this->hasMany(SlsOrder::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsOrgs()
    {
        return $this->hasMany(SlsOrg::className(), ['manager_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorders()
    {
        return $this->hasMany(SlsPreorder::className(), ['user_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsStatPrices()
    {
        return $this->hasMany(SlsStatPrice::className(), ['user_fk' => 'id']);
    }
}
