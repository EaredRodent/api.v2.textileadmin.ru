<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "pr_stor_fabric".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $state доступна для раскроя, в работе, обработана, возврат-брак
 * @property string $cutws
 * @property int $type_fk
 * @property int $color_fk
 * @property string $price
 * @property int $w_brutto
 * @property int $w_pack
 * @property int $w_netto
 * @property int $w_current
 * @property string $ts_incom
 * @property string $ts_inwork
 * @property string $ts_cut
 * @property string $ts_defect
 *
 * @property PrRollChunk[] $prRollChunks
 * @property RefFabricColor $colorFk
 * @property RefFabricType $typeFk
 */
class GiiPrStorFabric extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_stor_fabric';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_incom', 'ts_inwork', 'ts_cut', 'ts_defect'], 'safe'],
            [['state', 'cutws'], 'string'],
            [['type_fk', 'color_fk', 'price', 'w_brutto'], 'required'],
            [['type_fk', 'color_fk', 'w_brutto', 'w_pack', 'w_netto', 'w_current'], 'integer'],
            [['price'], 'number'],
            [['color_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefFabricColor::className(), 'targetAttribute' => ['color_fk' => 'id']],
            [['type_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefFabricType::className(), 'targetAttribute' => ['type_fk' => 'id']],
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
            'state' => 'State',
            'cutws' => 'Cutws',
            'type_fk' => 'Type Fk',
            'color_fk' => 'Color Fk',
            'price' => 'Price',
            'w_brutto' => 'W Brutto',
            'w_pack' => 'W Pack',
            'w_netto' => 'W Netto',
            'w_current' => 'W Current',
            'ts_incom' => 'Ts Incom',
            'ts_inwork' => 'Ts Inwork',
            'ts_cut' => 'Ts Cut',
            'ts_defect' => 'Ts Defect',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrRollChunks()
    {
        return $this->hasMany(PrRollChunk::className(), ['roll_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorFk()
    {
        return $this->hasOne(RefFabricColor::className(), ['id' => 'color_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeFk()
    {
        return $this->hasOne(RefFabricType::className(), ['id' => 'type_fk']);
    }
}
