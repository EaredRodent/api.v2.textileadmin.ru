<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsPreorder;
use app\modules\v1\models\v3\V3Box;
use app\modules\v1\models\v3\V3Invoice;
use Yii;

/**
 * This is the model class for table "v3_money_event".
 *
 * @property int $id
 * @property int $box_fk
 * @property string $direct
 * @property string $type
 * @property int $order_fk
 * @property int $preorder_fk
 * @property int $invoice_fk
 * @property int $trans_box_fk
 * @property string $summ
 * @property string $comment
 * @property string $state prep - стоит в очереди оплтаы. pay - свершившийся платеж
 * @property string $ts_create
 * @property string $ts_pay
 * @property string $ts_del
 *
 * @property SlsOrder $orderFk
 * @property SlsPreorder $preorderFk
 * @property V3Box $boxFk
 * @property V3Box $transBoxFk
 * @property V3Invoice $invoiceFk
 */
class GiiV3MoneyEvent extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v3_money_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['box_fk', 'direct', 'type', 'summ'], 'required'],
            [['box_fk', 'order_fk', 'preorder_fk', 'invoice_fk', 'trans_box_fk'], 'integer'],
            [['direct', 'type', 'state'], 'string'],
            [['summ'], 'number'],
            [['ts_create', 'ts_pay', 'ts_del'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['order_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsOrder::className(), 'targetAttribute' => ['order_fk' => 'id']],
            [['preorder_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsPreorder::className(), 'targetAttribute' => ['preorder_fk' => 'id']],
            [['box_fk'], 'exist', 'skipOnError' => true, 'targetClass' => V3Box::className(), 'targetAttribute' => ['box_fk' => 'id']],
            [['trans_box_fk'], 'exist', 'skipOnError' => true, 'targetClass' => V3Box::className(), 'targetAttribute' => ['trans_box_fk' => 'id']],
            [['invoice_fk'], 'exist', 'skipOnError' => true, 'targetClass' => V3Invoice::className(), 'targetAttribute' => ['invoice_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'box_fk' => 'Box Fk',
            'direct' => 'Direct',
            'type' => 'Type',
            'order_fk' => 'Order Fk',
            'preorder_fk' => 'Preorder Fk',
            'invoice_fk' => 'Invoice Fk',
            'trans_box_fk' => 'Trans Box Fk',
            'summ' => 'Summ',
            'comment' => 'Comment',
            'state' => 'State',
            'ts_create' => 'Ts Create',
            'ts_pay' => 'Ts Pay',
            'ts_del' => 'Ts Del',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderFk()
    {
        return $this->hasOne(SlsOrder::className(), ['id' => 'order_fk']);
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
    public function getBoxFk()
    {
        return $this->hasOne(V3Box::className(), ['id' => 'box_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransBoxFk()
    {
        return $this->hasOne(V3Box::className(), ['id' => 'trans_box_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceFk()
    {
        return $this->hasOne(V3Invoice::className(), ['id' => 'invoice_fk']);
    }
}
