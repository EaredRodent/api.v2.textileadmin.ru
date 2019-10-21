<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsPreorder;
use Yii;

/**
 * This is the model class for table "sls_order".
 *
 * @property int $id
 * @property int $contact_fk Конткатное лицо клиента B2B
 * @property int $user_fk Менеджер отдела продаж
 * @property string $ts_create
 * @property int $preorder_fk ссылка на предзаказ, если заказ сформировался из предзаказа
 * @property int $flag_return
 * @property int $flag_pre флаг предзаказа
 * @property int $flag_pre_prod предзаказ отправлен в производство
 * @property string $status
 * @property int $client_fk
 * @property string $pay_type
 * @property int $nds
 * @property string $pact_pay оплата: - предоплата - оплата по факту поставки - отсрочка оплаты - реализация
 * @property string $pact_deliv
 * @property string $pact_date
 * @property string $pact_other
 * @property string $ts_preorder время отправки предзаказа в обработку
 * @property string $ts_preorder_prod время, когда заказ отправлен в производство
 * @property string $ts_wait_assembl
 * @property string $ts_assembl
 * @property string $ts_wait
 * @property string $ts_result время утверждения или отклонения заявки
 * @property string $ts_doc время создания документов
 * @property string $ts_pay
 * @property string $ts_send
 * @property string $summ_order
 * @property string $summ_pay
 * @property int $flag_pay
 * @property string $transp_comp транспортная компания
 * @property string $addr_delivery адрес доставки
 * @property string $ts_send_manager время когда клиент отправляет заказ на согласование менеджеру
 *
 * @property PrStorProd[] $prStorProds
 * @property PrTsdTask[] $prTsdTasks
 * @property SlsItem[] $slsItems
 * @property SlsMoney[] $slsMoneys
 * @property AnxUser $userFk
 * @property AnxUser $contactFk
 * @property SlsPreorder $preorderFk
 * @property SlsClient $clientFk
 */
class GiiSlsOrder extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_fk', 'user_fk', 'preorder_fk', 'flag_return', 'flag_pre', 'flag_pre_prod', 'client_fk', 'nds', 'flag_pay'], 'integer'],
            [['ts_create', 'pact_date', 'ts_preorder', 'ts_preorder_prod', 'ts_wait_assembl', 'ts_assembl', 'ts_wait', 'ts_result', 'ts_doc', 'ts_pay', 'ts_send', 'ts_send_manager'], 'safe'],
            [['status', 'pay_type', 'pact_pay', 'pact_deliv', 'pact_other'], 'string'],
            [['client_fk'], 'required'],
            [['summ_order', 'summ_pay'], 'number'],
            [['transp_comp', 'addr_delivery'], 'string', 'max' => 255],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['contact_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['contact_fk' => 'id']],
            [['preorder_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsPreorder::className(), 'targetAttribute' => ['preorder_fk' => 'id']],
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
            'contact_fk' => 'Contact Fk',
            'user_fk' => 'User Fk',
            'ts_create' => 'Ts Create',
            'preorder_fk' => 'Preorder Fk',
            'flag_return' => 'Flag Return',
            'flag_pre' => 'Flag Pre',
            'flag_pre_prod' => 'Flag Pre Prod',
            'status' => 'Status',
            'client_fk' => 'Client Fk',
            'pay_type' => 'Pay Type',
            'nds' => 'Nds',
            'pact_pay' => 'Pact Pay',
            'pact_deliv' => 'Pact Deliv',
            'pact_date' => 'Pact Date',
            'pact_other' => 'Pact Other',
            'ts_preorder' => 'Ts Preorder',
            'ts_preorder_prod' => 'Ts Preorder Prod',
            'ts_wait_assembl' => 'Ts Wait Assembl',
            'ts_assembl' => 'Ts Assembl',
            'ts_wait' => 'Ts Wait',
            'ts_result' => 'Ts Result',
            'ts_doc' => 'Ts Doc',
            'ts_pay' => 'Ts Pay',
            'ts_send' => 'Ts Send',
            'summ_order' => 'Summ Order',
            'summ_pay' => 'Summ Pay',
            'flag_pay' => 'Flag Pay',
            'transp_comp' => 'Transp Comp',
            'addr_delivery' => 'Addr Delivery',
            'ts_send_manager' => 'Ts Send Manager',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrStorProds()
    {
        return $this->hasMany(PrStorProd::className(), ['order_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrTsdTasks()
    {
        return $this->hasMany(PrTsdTask::className(), ['order_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsItems()
    {
        return $this->hasMany(SlsItem::className(), ['order_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['order_fk' => 'id']);
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
    public function getContactFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'contact_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreorderFk()
    {
        return $this->hasOne(SlsPreorder::className(), ['id' => 'preorder_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientFk()
    {
        return $this->hasOne(SlsClient::className(), ['id' => 'client_fk']);
    }
}
