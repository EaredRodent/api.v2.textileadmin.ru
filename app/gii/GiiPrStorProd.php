<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProdPack;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\sls\SlsOrder;
use Yii;

/**
 * This is the model class for table "pr_stor_prod".
 *
 * @property int $id
 * @property int $user_fk
 * @property int $order_fk
 * @property int $waybill_fk
 * @property int $act_cut_fk
 * @property int $invent_fk
 * @property string $dt_create
 * @property string $direct
 * @property string $type
 * @property int $exec
 * @property string $dt_move
 * @property int $blank_fk
 * @property int $print_fk
 * @property int $pack_fk
 * @property int $size_5xs
 * @property int $size_4xs
 * @property int $size_3xs
 * @property int $size_2xs
 * @property int $size_xs
 * @property int $size_s
 * @property int $size_m
 * @property int $size_l
 * @property int $size_xl
 * @property int $size_2xl
 * @property int $size_3xl
 * @property int $size_4xl
 * @property int $cost_unit
 * @property int $client_fk
 * @property string $invoice
 * @property string $comment
 *
 * @property PrActCut $actCutFk
 * @property PrWaybill $waybillFk
 * @property SlsOrder $orderFk
 * @property RefArtBlank $blankFk
 * @property RefProdPrint $printFk
 * @property RefProdPack $packFk
 */
class GiiPrStorProd extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_stor_prod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_fk', 'direct', 'type', 'blank_fk', 'print_fk'], 'required'],
            [['user_fk', 'order_fk', 'waybill_fk', 'act_cut_fk', 'invent_fk', 'exec', 'blank_fk', 'print_fk', 'pack_fk', 'size_5xs', 'size_4xs', 'size_3xs', 'size_2xs', 'size_xs', 'size_s', 'size_m', 'size_l', 'size_xl', 'size_2xl', 'size_3xl', 'size_4xl', 'cost_unit', 'client_fk'], 'integer'],
            [['dt_create', 'dt_move'], 'safe'],
            [['direct', 'type'], 'string'],
            [['invoice'], 'string', 'max' => 45],
            [['comment'], 'string', 'max' => 245],
            [['act_cut_fk'], 'exist', 'skipOnError' => true, 'targetClass' => PrActCut::className(), 'targetAttribute' => ['act_cut_fk' => 'id']],
            [['waybill_fk'], 'exist', 'skipOnError' => true, 'targetClass' => PrWaybill::className(), 'targetAttribute' => ['waybill_fk' => 'id']],
            [['order_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsOrder::className(), 'targetAttribute' => ['order_fk' => 'id']],
            [['blank_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefArtBlank::className(), 'targetAttribute' => ['blank_fk' => 'id']],
            [['print_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefProdPrint::className(), 'targetAttribute' => ['print_fk' => 'id']],
            [['pack_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefProdPack::className(), 'targetAttribute' => ['pack_fk' => 'id']],
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
            'order_fk' => 'Order Fk',
            'waybill_fk' => 'Waybill Fk',
            'act_cut_fk' => 'Act Cut Fk',
            'invent_fk' => 'Invent Fk',
            'dt_create' => 'Dt Create',
            'direct' => 'Direct',
            'type' => 'Type',
            'exec' => 'Exec',
            'dt_move' => 'Dt Move',
            'blank_fk' => 'Blank Fk',
            'print_fk' => 'Print Fk',
            'pack_fk' => 'Pack Fk',
            'size_5xs' => 'Size 5xs',
            'size_4xs' => 'Size 4xs',
            'size_3xs' => 'Size 3xs',
            'size_2xs' => 'Size 2xs',
            'size_xs' => 'Size Xs',
            'size_s' => 'Size S',
            'size_m' => 'Size M',
            'size_l' => 'Size L',
            'size_xl' => 'Size Xl',
            'size_2xl' => 'Size 2xl',
            'size_3xl' => 'Size 3xl',
            'size_4xl' => 'Size 4xl',
            'cost_unit' => 'Cost Unit',
            'client_fk' => 'Client Fk',
            'invoice' => 'Invoice',
            'comment' => 'Comment',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActCutFk()
    {
        return $this->hasOne(PrActCut::className(), ['id' => 'act_cut_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWaybillFk()
    {
        return $this->hasOne(PrWaybill::className(), ['id' => 'waybill_fk']);
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
    public function getBlankFk()
    {
        return $this->hasOne(RefArtBlank::className(), ['id' => 'blank_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrintFk()
    {
        return $this->hasOne(RefProdPrint::className(), ['id' => 'print_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackFk()
    {
        return $this->hasOne(RefProdPack::className(), ['id' => 'pack_fk']);
    }
}
