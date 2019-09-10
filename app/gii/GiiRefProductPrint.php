<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProdPrint;
use Yii;

/**
 * This is the model class for table "ref_product_print".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $ts_update
 * @property int $blank_fk
 * @property int $print_fk
 * @property int $flag_price
 * @property int $flag_price_on
 * @property int $flag_bazar
 * @property int $flag_bazar_on
 * @property int $price_5xs
 * @property int $price_4xs
 * @property int $price_3xs
 * @property int $price_2xs
 * @property int $price_xs
 * @property int $price_s
 * @property int $price_m
 * @property int $price_l
 * @property int $price_xl
 * @property int $price_2xl
 * @property int $price_3xl
 * @property int $price_4xl
 *
 * @property RefArtBlank $blankFk
 * @property RefProdPrint $printFk
 */
class GiiRefProductPrint extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_product_print';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_update'], 'safe'],
            [['blank_fk', 'print_fk'], 'required'],
            [['blank_fk', 'print_fk', 'flag_price', 'flag_price_on', 'flag_bazar', 'flag_bazar_on', 'price_5xs', 'price_4xs', 'price_3xs', 'price_2xs', 'price_xs', 'price_s', 'price_m', 'price_l', 'price_xl', 'price_2xl', 'price_3xl', 'price_4xl'], 'integer'],
            [['blank_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefArtBlank::className(), 'targetAttribute' => ['blank_fk' => 'id']],
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
            'blank_fk' => 'Blank Fk',
            'print_fk' => 'Print Fk',
            'flag_price' => 'Flag Price',
            'flag_price_on' => 'Flag Price On',
            'flag_bazar' => 'Flag Bazar',
            'flag_bazar_on' => 'Flag Bazar On',
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
        ];
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
}
