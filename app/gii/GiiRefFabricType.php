<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "ref_fabric_type".
 *
 * @property int $id
 * @property string $dt_create
 * @property string $type
 * @property string $type_price
 * @property string|null $type_en
 * @property string|null $struct
 * @property string|null $struct_en
 * @property int|null $desity
 * @property string|null $epithets
 * @property string|null $care1
 * @property string|null $care2
 * @property string|null $care3
 * @property string|null $care4
 * @property string|null $care5
 * @property string|null $care6
 * @property string|null $collection
 *
 * @property PrStorFabric[] $prStorFabrics
 * @property RefArtBlank[] $refArtBlanks
 * @property RefWeight[] $refWeights
 * @property SpecCurve[] $specCurves
 */
class GiiRefFabricType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_fabric_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['type'], 'required'],
            [['desity'], 'integer'],
            [['epithets'], 'string'],
            [['type', 'type_price', 'type_en'], 'string', 'max' => 45],
            [['struct', 'struct_en'], 'string', 'max' => 245],
            [['care1', 'care2', 'care3', 'care4', 'care5', 'care6'], 'string', 'max' => 25],
            [['collection'], 'string', 'max' => 100],
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
            'type' => 'Type',
            'type_price' => 'Type Price',
            'type_en' => 'Type En',
            'struct' => 'Struct',
            'struct_en' => 'Struct En',
            'desity' => 'Desity',
            'epithets' => 'Epithets',
            'care1' => 'Care1',
            'care2' => 'Care2',
            'care3' => 'Care3',
            'care4' => 'Care4',
            'care5' => 'Care5',
            'care6' => 'Care6',
            'collection' => 'Collection',
        ];
    }

    /**
     * Gets query for [[PrStorFabrics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrStorFabrics()
    {
        return $this->hasMany(PrStorFabric::className(), ['type_fk' => 'id']);
    }

    /**
     * Gets query for [[RefArtBlanks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefArtBlanks()
    {
        return $this->hasMany(RefArtBlank::className(), ['fabric_type_fk' => 'id']);
    }

    /**
     * Gets query for [[RefWeights]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefWeights()
    {
        return $this->hasMany(RefWeight::className(), ['fabric_fk' => 'id']);
    }

    /**
     * Gets query for [[SpecCurves]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecCurves()
    {
        return $this->hasMany(SpecCurve::className(), ['fabric_fk' => 'id']);
    }
}
