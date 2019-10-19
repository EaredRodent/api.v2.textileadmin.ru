<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProdPack;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\sls\SlsOrder;
use Yii;

/**
 * This is the model class for table "sls_item".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $ts_update
 * @property int $pre_order_id резерв для предзаказов
 * @property int $order_fk резерв для обычных заказов
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
 * @property string $price_5xs
 * @property string $price_4xs
 * @property string $price_3xs
 * @property string $price_2xs
 * @property string $price_xs
 * @property string $price_s
 * @property string $price_m
 * @property string $price_l
 * @property string $price_xl
 * @property string $price_2xl
 * @property string $price_3xl
 * @property string $price_4xl
 * @property string $discount
 * @property string $discount2 скидка заказа. общая цена (база-скидка1)-скидка2
 *
 * @property SlsOrder $orderFk
 * @property RefArtBlank $blankFk
 * @property RefProdPack $packFk
 * @property RefProdPrint $printFk
 */
class GiiSlsItem extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_update'], 'safe'],
            [['pre_order_id', 'order_fk', 'blank_fk', 'print_fk', 'pack_fk', 'size_5xs', 'size_4xs', 'size_3xs', 'size_2xs', 'size_xs', 'size_s', 'size_m', 'size_l', 'size_xl', 'size_2xl', 'size_3xl', 'size_4xl'], 'integer'],
            [['blank_fk', 'print_fk', 'pack_fk'], 'required'],
            [['price_5xs', 'price_4xs', 'price_3xs', 'price_2xs', 'price_xs', 'price_s', 'price_m', 'price_l', 'price_xl', 'price_2xl', 'price_3xl', 'price_4xl', 'discount', 'discount2'], 'number'],
            [['order_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsOrder::className(), 'targetAttribute' => ['order_fk' => 'id']],
            [['blank_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefArtBlank::className(), 'targetAttribute' => ['blank_fk' => 'id']],
            [['pack_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefProdPack::className(), 'targetAttribute' => ['pack_fk' => 'id']],
            [['print_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefProdPrint::className(), 'targetAttribute' => ['print_fk' => 'id']],
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
            'pre_order_id' => 'Pre Order ID',
            'order_fk' => 'Order Fk',
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
            'price_5xs' => 'Price 5xs',
            'price_4xs' => 'Price 4xs',
            'price_3xs' => 'Price 3xs',
            'price_2xs' => 'Price 2xs',
            'price_xs' => 'Price Xs',
            'price_s' => 'Price S',
            'price_m' => 'Price M',
            'price_l' => 'Price L',
            'price_xl' => 'Price Xl',
            'price_2xl' => 'Price 2xl',
            'price_3xl' => 'Price 3xl',
            'price_4xl' => 'Price 4xl',
            'discount' => 'Discount',
            'discount2' => 'Discount2',
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
    public function getBlankFk()
    {
        return $this->hasOne(RefArtBlank::className(), ['id' => 'blank_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackFk()
    {
        return $this->hasOne(RefProdPack::className(), ['id' => 'pack_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrintFk()
    {
        return $this->hasOne(RefProdPrint::className(), ['id' => 'print_fk']);
    }
}
