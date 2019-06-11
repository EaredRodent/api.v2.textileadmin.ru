<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsPayItem;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sls_money".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $user_fk
 * @property string $ts_incom
 * @property string $type
 * @property string $direct
 * @property int $pay_item_fk
 * @property string $summ
 * @property int $order_fk
 * @property int $invoice_fk
 * @property string $comment
 *
 * @property AnxUser $userFk
 * @property SlsInvoice $invoiceFk
 * @property SlsOrder $orderFk
 * @property SlsPayItem $payItemFk
 */
class GiiSlsMoney extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_money';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_incom'], 'safe'],
            [['user_fk', 'type', 'direct', 'summ'], 'required'],
            [['user_fk', 'pay_item_fk', 'order_fk', 'invoice_fk'], 'integer'],
            [['type', 'direct'], 'string'],
            [['summ'], 'number'],
            [['comment'], 'string', 'max' => 255],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['invoice_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsInvoice::className(), 'targetAttribute' => ['invoice_fk' => 'id']],
            [['order_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsOrder::className(), 'targetAttribute' => ['order_fk' => 'id']],
            [['pay_item_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsPayItem::className(), 'targetAttribute' => ['pay_item_fk' => 'id']],
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
            'ts_incom' => 'Ts Incom',
            'type' => 'Type',
            'direct' => 'Direct',
            'pay_item_fk' => 'Pay Item Fk',
            'summ' => 'Summ',
            'order_fk' => 'Order Fk',
            'invoice_fk' => 'Invoice Fk',
            'comment' => 'Comment',
        ];
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
    public function getInvoiceFk()
    {
        return $this->hasOne(SlsInvoice::className(), ['id' => 'invoice_fk']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderFk()
    {
        return $this->hasOne(SlsOrder::className(), ['id' => 'order_fk']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayItemFk()
    {
        return $this->hasOne(SlsPayItem::className(), ['id' => 'pay_item_fk']);
    }
}
