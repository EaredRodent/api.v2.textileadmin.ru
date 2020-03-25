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
 * @property int|null $size_5xs
 * @property int|null $size_4xs
 * @property int|null $size_3xs
 * @property int|null $size_2xs
 * @property int|null $size_xs
 * @property int|null $size_s
 * @property int|null $size_m
 * @property int|null $size_l
 * @property int|null $size_xl
 * @property int|null $size_2xl
 * @property int|null $size_3xl
 * @property int|null $size_4xl
 * @property string|null $epithets
 *
 * @property RefBlankModel $modelFk
 * @property RefFabricType $fabricFk
 */
class GiiRefWeight extends \yii\db\ActiveRecord
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
            [['epithets'], 'string'],
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
            'epithets' => 'Epithets',
        ];
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
     * Gets query for [[FabricFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabricFk()
    {
        return $this->hasOne(RefFabricType::className(), ['id' => 'fabric_fk']);
    }
}
