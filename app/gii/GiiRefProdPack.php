<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "ref_prod_pack".
 *
 * @property int $id
 * @property string $dt_create
 * @property string $title
 *
 * @property PrInventItem[] $prInventItems
 * @property PrLot[] $prLots
 * @property PrStorProd[] $prStorProds
 * @property PrTaskCutItem[] $prTaskCutItems
 * @property PrWaybillItem[] $prWaybillItems
 * @property PrWsCut[] $prWsCuts
 * @property RefEan[] $refEans
 * @property SlsItem[] $slsItems
 * @property SlsPreorderItem[] $slsPreorderItems
 * @property SlsPreorderReserv[] $slsPreorderReservs
 */
class GiiRefProdPack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_prod_pack';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrInventItems()
    {
        return $this->hasMany(PrInventItem::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrLots()
    {
        return $this->hasMany(PrLot::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrStorProds()
    {
        return $this->hasMany(PrStorProd::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrTaskCutItems()
    {
        return $this->hasMany(PrTaskCutItem::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrWaybillItems()
    {
        return $this->hasMany(PrWaybillItem::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrWsCuts()
    {
        return $this->hasMany(PrWsCut::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefEans()
    {
        return $this->hasMany(RefEan::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsItems()
    {
        return $this->hasMany(SlsItem::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorderItems()
    {
        return $this->hasMany(SlsPreorderItem::className(), ['pack_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorderReservs()
    {
        return $this->hasMany(SlsPreorderReserv::className(), ['pack_fk' => 'id']);
    }
}
