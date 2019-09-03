<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefFabricType;
use Yii;

/**
 * This is the model class for table "ref_weight".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $model_fk
 * @property int $fabric_fk
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
 *
 * @property RefBlankModel $modelFk
 * @property RefFabricType $fabricFk
 */
class GiiRefWeight extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_weight';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['model_fk', 'fabric_fk'], 'required'],
            [['model_fk', 'fabric_fk', 'size_5xs', 'size_4xs', 'size_3xs', 'size_2xs', 'size_xs', 'size_s', 'size_m', 'size_l', 'size_xl', 'size_2xl', 'size_3xl', 'size_4xl'], 'integer'],
            [['model_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefBlankModel::className(), 'targetAttribute' => ['model_fk' => 'id']],
            [['fabric_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefFabricType::className(), 'targetAttribute' => ['fabric_fk' => 'id']],
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
            'model_fk' => 'Model Fk',
            'fabric_fk' => 'Fabric Fk',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelFk()
    {
        return $this->hasOne(RefBlankModel::className(), ['id' => 'model_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFabricFk()
    {
        return $this->hasOne(RefFabricType::className(), ['id' => 'fabric_fk']);
    }
}
