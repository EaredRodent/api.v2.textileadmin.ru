<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProdPack;
use app\modules\v1\models\ref\RefProdPrint;
use Yii;

/**
 * This is the model class for table "ref_ean".
 *
 * @property int $id
 * @property string $dt_create
 * @property int $blank_fk
 * @property int $print_fk
 * @property int $pack_fk
 * @property string $size
 *
 * @property PrTsdItem[] $prTsdItems
 * @property RefArtBlank $blankFk
 * @property RefProdPack $packFk
 * @property RefProdPrint $printFk
 */
class GiiRefEan extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_ean';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['blank_fk', 'print_fk', 'pack_fk', 'size'], 'required'],
            [['blank_fk', 'print_fk', 'pack_fk'], 'integer'],
            [['size'], 'string', 'max' => 10],
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
            'dt_create' => 'Dt Create',
            'blank_fk' => 'Blank Fk',
            'print_fk' => 'Print Fk',
            'pack_fk' => 'Pack Fk',
            'size' => 'Size',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrTsdItems()
    {
        return $this->hasMany(PrTsdItem::className(), ['prod_fk' => 'id']);
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
