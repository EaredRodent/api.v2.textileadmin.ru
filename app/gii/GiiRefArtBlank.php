<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefBlankTheme;
use app\modules\v1\models\ref\RefCollection;
use app\modules\v1\models\ref\RefDescript;
use app\modules\v1\models\ref\RefFabricType;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsItem;
use Yii;

/**
 * This is the model class for table "ref_art_blank".
 *
 * @property int $id
 * @property string $dt_create
 * @property int $model_fk
 * @property int $fabric_type_fk
 * @property int $theme_fk
 * @property string|null $comment
 * @property int|null $weight_fabric
 * @property int $flag_price
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
 * @property int|null $flag_best_photo
 * @property int|null $min_rest
 * @property int|null $mid_rest
 * @property string|null $assortment
 * @property int $flag_stop_prod
 * @property int $discount
 * @property int|null $collection_fk
 * @property int|null $descript_fk
 *
 * @property PrInventItem[] $prInventItems
 * @property PrLot[] $prLots
 * @property PrStorProd[] $prStorProds
 * @property PrTaskCutItem[] $prTaskCutItems
 * @property PrWaybillItem[] $prWaybillItems
 * @property PrWsCut[] $prWsCuts
 * @property RefCollection $collectionFk
 * @property RefDescript $descriptFk
 * @property RefBlankModel $modelFk
 * @property RefFabricType $fabricTypeFk
 * @property RefBlankTheme $themeFk
 * @property RefEan[] $refEans
 * @property RefProductPrint[] $refProductPrints
 * @property SlsItem[] $slsItems
 * @property SlsPreorderItem[] $slsPreorderItems
 * @property SlsPreorderReserv[] $slsPreorderReservs
 */
class GiiRefArtBlank extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_art_blank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['model_fk', 'fabric_type_fk', 'theme_fk'], 'required'],
            [['model_fk', 'fabric_type_fk', 'theme_fk', 'weight_fabric', 'flag_price', 'price_5xs', 'price_4xs', 'price_3xs', 'price_2xs', 'price_xs', 'price_s', 'price_m', 'price_l', 'price_xl', 'price_2xl', 'price_3xl', 'price_4xl', 'flag_best_photo', 'min_rest', 'mid_rest', 'flag_stop_prod', 'discount', 'collection_fk', 'descript_fk'], 'integer'],
            [['assortment'], 'string'],
            [['comment'], 'string', 'max' => 245],
            [['collection_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefCollection::className(), 'targetAttribute' => ['collection_fk' => 'id']],
            [['descript_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefDescript::className(), 'targetAttribute' => ['descript_fk' => 'id']],
            [['model_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefBlankModel::className(), 'targetAttribute' => ['model_fk' => 'id']],
            [['fabric_type_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefFabricType::className(), 'targetAttribute' => ['fabric_type_fk' => 'id']],
            [['theme_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefBlankTheme::className(), 'targetAttribute' => ['theme_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dt_create' => 'Dt Create',
            'model_fk' => 'Model Fk',
            'fabric_type_fk' => 'Fabric Type Fk',
            'theme_fk' => 'Theme Fk',
            'comment' => 'Comment',
            'weight_fabric' => 'Weight Fabric',
            'flag_price' => 'Flag Price',
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
            'flag_best_photo' => 'Flag Best Photo',
            'min_rest' => 'Min Rest',
            'mid_rest' => 'Mid Rest',
            'assortment' => 'Assortment',
            'flag_stop_prod' => 'Flag Stop Prod',
            'discount' => 'Discount',
            'collection_fk' => 'Collection Fk',
            'descript_fk' => 'Descript Fk',
        ];
    }

    /**
     * Gets query for [[PrInventItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrInventItems()
    {
        return $this->hasMany(PrInventItem::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[PrLots]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrLots()
    {
        return $this->hasMany(PrLot::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[PrStorProds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrStorProds()
    {
        return $this->hasMany(PrStorProd::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[PrTaskCutItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrTaskCutItems()
    {
        return $this->hasMany(PrTaskCutItem::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[PrWaybillItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrWaybillItems()
    {
        return $this->hasMany(PrWaybillItem::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[PrWsCuts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrWsCuts()
    {
        return $this->hasMany(PrWsCut::className(), ['blank_fk' => 'id']);
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
     * Gets query for [[ModelFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModelFk()
    {
        return $this->hasOne(RefBlankModel::className(), ['id' => 'model_fk']);
    }

    /**
     * Gets query for [[FabricTypeFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabricTypeFk()
    {
        return $this->hasOne(RefFabricType::className(), ['id' => 'fabric_type_fk']);
    }

    /**
     * Gets query for [[ThemeFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThemeFk()
    {
        return $this->hasOne(RefBlankTheme::className(), ['id' => 'theme_fk']);
    }

    /**
     * Gets query for [[RefEans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefEans()
    {
        return $this->hasMany(RefEan::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[RefProductPrints]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefProductPrints()
    {
        return $this->hasMany(RefProductPrint::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[SlsItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlsItems()
    {
        return $this->hasMany(SlsItem::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[SlsPreorderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorderItems()
    {
        return $this->hasMany(SlsPreorderItem::className(), ['blank_fk' => 'id']);
    }

    /**
     * Gets query for [[SlsPreorderReservs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorderReservs()
    {
        return $this->hasMany(SlsPreorderReserv::className(), ['blank_fk' => 'id']);
    }
}
