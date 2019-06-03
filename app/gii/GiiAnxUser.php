<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "anx_user".
 *
 * @property int $id
 * @property string $login
 * @property string $name
 * @property string $role
 * @property int $status
 * @property string $hash
 * @property string $auth_key
 * @property string $accesstoken
 *
 * @property AmfilesDirectory[] $amfilesDirectories
 * @property AmfilesFile[] $amfilesFiles
 * @property AnxCmdLog[] $anxCmdLogs
 * @property AnxDbLog[] $anxDbLogs
 * @property PrInventItem[] $prInventItems
 * @property PrTsdItem[] $prTsdItems
 * @property SlsClient[] $slsClients
 * @property SlsInvoice[] $slsInvoices
 * @property SlsMoney[] $slsMoneys
 * @property SlsOrder[] $slsOrders
 * @property SlsStatPrice[] $slsStatPrices
 */
class GiiAnxUser extends \yii\db\ActiveRecord
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
            [['login', 'name', 'role', 'status', 'hash', 'auth_key'], 'required'],
            [['status'], 'integer'],
            [['login', 'name', 'role', 'auth_key'], 'string', 'max' => 45],
            [['hash'], 'string', 'max' => 60],
            [['accesstoken'], 'string', 'max' => 128],
            [['login'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'name' => 'Name',
            'role' => 'Role',
            'status' => 'Status',
            'hash' => 'Hash',
            'auth_key' => 'Auth Key',
            'accesstoken' => 'Accesstoken',
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
    public function getSlsStatPrices()
    {
        return $this->hasMany(SlsStatPrice::className(), ['user_fk' => 'id']);
    }
}
