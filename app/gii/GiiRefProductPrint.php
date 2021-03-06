<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefCollection;
use app\modules\v1\models\ref\RefDescript;
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
 * @property int|null $price_5xs
 * @property int|null $price_4xs
 * @property int|null $price_3xs
 * @property int|null $price_2xs
 * @property int|null $price_xs
 * @property int|null $price_s
 * @property int|null $price_m
 * @property int|null $price_l
 * @property int|null $price_xl
 * @property int|null $price_2xl
 * @property int|null $price_3xl
 * @property int|null $price_4xl
 * @property string|null $assortiment
 * @property int $flag_stop_prod
 * @property int $discount
 * @property int|null $collection_fk
 * @property int|null $descript_fk
 *
 * @property RefArtBlank $blankFk
 * @property RefCollection $collectionFk
 * @property RefDescript $descriptFk
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
            [['blank_fk', 'print_fk', 'flag_price', 'flag_price_on', 'flag_bazar', 'flag_bazar_on', 'price_5xs', 'price_4xs', 'price_3xs', 'price_2xs', 'price_xs', 'price_s', 'price_m', 'price_l', 'price_xl', 'price_2xl', 'price_3xl', 'price_4xl', 'flag_stop_prod', 'discount', 'collection_fk', 'descript_fk'], 'integer'],
            [['assortiment'], 'string'],
            [['blank_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefArtBlank::className(), 'targetAttribute' => ['blank_fk' => 'id']],
            [['collection_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefCollection::className(), 'targetAttribute' => ['collection_fk' => 'id']],
            [['descript_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefDescript::className(), 'targetAttribute' => ['descript_fk' => 'id']],
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
            'assortiment' => 'Assortiment',
            'flag_stop_prod' => 'Flag Stop Prod',
            'discount' => 'Discount',
            'collection_fk' => 'Collection Fk',
            'descript_fk' => 'Descript Fk',
        ];
    }

    /**
     * Gets query for [[BlankFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlankFk()
    {
        return $this->hasOne(RefArtBlank::className(), ['id' => 'blank_fk']);
    }

    /**
     * Gets query for [[CollectionFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollectionFk()
    {
        return $this->hasOne(RefCollection::className(), ['id' => 'collection_fk']);
    }

    /**
     * Gets query for [[DescriptFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptFk()
    {
        return $this->hasOne(RefDescript::className(), ['id' => 'descript_fk']);
    }

    /**
     * Gets query for [[PrintFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrintFk()
    {
        return $this->hasOne(RefProdPrint::className(), ['id' => 'print_fk']);
    }
}
