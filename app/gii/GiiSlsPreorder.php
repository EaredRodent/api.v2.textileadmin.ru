<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsClient;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sls_preorder".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $ts_update
 * @property int $user_fk
 * @property int $client_fk
 * @property string $state
 * @property string $pay_type
 * @property int $nds
 * @property string $pact_pay
 * @property string $pact_deliv
 * @property string $pact_date
 * @property string $pact_other
 * @property string $ts_inprod
 * @property string $ts_done
 * @property string $ts_del
 * @property string $ts_money время создания документов БЕЗНАЛ или поступления НАЛ
 *
 * @property SlsMoney[] $slsMoneys
 * @property SlsOrder[] $slsOrders
 * @property AnxUser $userFk
 * @property SlsClient $clientFk
 * @property SlsPreorderItem[] $slsPreorderItems
 * @property SlsPreorderReserv[] $slsPreorderReservs
 */
class GiiSlsPreorder extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_preorder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_update', 'pact_date', 'ts_inprod', 'ts_done', 'ts_del', 'ts_money'], 'safe'],
            [['user_fk', 'client_fk'], 'required'],
            [['user_fk', 'client_fk', 'nds'], 'integer'],
            [['state', 'pay_type', 'pact_pay', 'pact_deliv', 'pact_other'], 'string'],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['client_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsClient::className(), 'targetAttribute' => ['client_fk' => 'id']],
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
            'ts_update' => 'Ts Update',
            'user_fk' => 'User Fk',
            'client_fk' => 'Client Fk',
            'state' => 'State',
            'pay_type' => 'Pay Type',
            'nds' => 'Nds',
            'pact_pay' => 'Pact Pay',
            'pact_deliv' => 'Pact Deliv',
            'pact_date' => 'Pact Date',
            'pact_other' => 'Pact Other',
            'ts_inprod' => 'Ts Inprod',
            'ts_done' => 'Ts Done',
            'ts_del' => 'Ts Del',
            'ts_money' => 'Ts Money',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['preorder_fk' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSlsOrders()
    {
        return $this->hasMany(SlsOrder::className(), ['preorder_fk' => 'id']);
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
    public function getClientFk()
    {
        return $this->hasOne(SlsClient::className(), ['id' => 'client_fk']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSlsPreorderItems()
    {
        return $this->hasMany(SlsPreorderItem::className(), ['preorder_fk' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSlsPreorderReservs()
    {
        return $this->hasMany(SlsPreorderReserv::className(), ['preorder_fk' => 'id']);
    }
}
