<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "ref_prod_print".
 *
 * @property int $id
 * @property string $dt_create
 * @property string $title
 * @property string $oxouno название для магазина
 *
 * @property PrInventItem[] $prInventItems
 * @property PrLot[] $prLots
 * @property PrStorProd[] $prStorProds
 * @property PrTaskCutItem[] $prTaskCutItems
 * @property PrWaybillItem[] $prWaybillItems
 * @property PrWsCut[] $prWsCuts
 * @property RefEan[] $refEans
 * @property RefPostLink[] $refPostLinks
 * @property RefProductPrint[] $refProductPrints
 * @property SlsItem[] $slsItems
 * @property SlsPreorderItem[] $slsPreorderItems
 * @property SlsPreorderReserv[] $slsPreorderReservs
 */
class GiiRefProdPrint extends \app\modules\v1\classes\ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_prod_print';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['title'], 'required'],
            [['title', 'oxouno'], 'string', 'max' => 45],
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
            'title' => 'Title',
            'oxouno' => 'Oxouno',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrInventItems()
    {
        return $this->hasMany(PrInventItem::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrLots()
    {
        return $this->hasMany(PrLot::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrStorProds()
    {
        return $this->hasMany(PrStorProd::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrTaskCutItems()
    {
        return $this->hasMany(PrTaskCutItem::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrWaybillItems()
    {
        return $this->hasMany(PrWaybillItem::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrWsCuts()
    {
        return $this->hasMany(PrWsCut::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefEans()
    {
        return $this->hasMany(RefEan::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefPostLinks()
    {
        return $this->hasMany(RefPostLink::className(), ['art_post_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefProductPrints()
    {
        return $this->hasMany(RefProductPrint::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsItems()
    {
        return $this->hasMany(SlsItem::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorderItems()
    {
        return $this->hasMany(SlsPreorderItem::className(), ['print_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorderReservs()
    {
        return $this->hasMany(SlsPreorderReserv::className(), ['print_fk' => 'id']);
    }
}
