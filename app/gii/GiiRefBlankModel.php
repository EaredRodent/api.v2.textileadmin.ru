<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankClass;
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefWeight;
use Yii;

/**
 * This is the model class for table "ref_blank_model".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $class_fk
 * @property int $sex_fk
 * @property int $sort
 * @property string $title
 * @property string|null $title_en
 * @property string|null $descript
 * @property string $cut1
 * @property string|null $cut2
 * @property string|null $cut3
 * @property string|null $cut4
 * @property string|null $cut5
 * @property string|null $epithets
 * @property string|null $fashion
 *
 * @property RefArtBlank[] $refArtBlanks
 * @property RefBlankClass $classFk
 * @property RefBlankSex $sexFk
 * @property RefWeight[] $refWeights
 * @property SpecCurve[] $specCurves
 * @property TsScheme[] $tsSchemes
 */
class GiiRefBlankModel extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_blank_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['class_fk', 'sex_fk', 'title'], 'required'],
            [['class_fk', 'sex_fk', 'sort'], 'integer'],
            [['epithets'], 'string'],
            [['title', 'title_en', 'fashion'], 'string', 'max' => 45],
            [['descript'], 'string', 'max' => 300],
            [['cut1', 'cut2', 'cut3', 'cut4', 'cut5'], 'string', 'max' => 100],
            [['class_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefBlankClass::className(), 'targetAttribute' => ['class_fk' => 'id']],
            [['sex_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefBlankSex::className(), 'targetAttribute' => ['sex_fk' => 'id']],
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
            'class_fk' => 'Class Fk',
            'sex_fk' => 'Sex Fk',
            'sort' => 'Sort',
            'title' => 'Title',
            'title_en' => 'Title En',
            'descript' => 'Descript',
            'cut1' => 'Cut1',
            'cut2' => 'Cut2',
            'cut3' => 'Cut3',
            'cut4' => 'Cut4',
            'cut5' => 'Cut5',
            'epithets' => 'Epithets',
            'fashion' => 'Fashion',
        ];
    }

    /**
     * Gets query for [[RefArtBlanks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefArtBlanks()
    {
        return $this->hasMany(RefArtBlank::className(), ['model_fk' => 'id']);
    }

    /**
     * Gets query for [[ClassFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClassFk()
    {
        return $this->hasOne(RefBlankClass::className(), ['id' => 'class_fk']);
    }

    /**
     * Gets query for [[SexFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSexFk()
    {
        return $this->hasOne(RefBlankSex::className(), ['id' => 'sex_fk']);
    }

    /**
     * Gets query for [[RefWeights]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefWeights()
    {
        return $this->hasMany(RefWeight::className(), ['model_fk' => 'id']);
    }

    /**
     * Gets query for [[SpecCurves]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecCurves()
    {
        return $this->hasMany(SpecCurve::className(), ['model_fk' => 'id']);
    }

    /**
     * Gets query for [[TsSchemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTsSchemes()
    {
        return $this->hasMany(TsScheme::className(), ['model_fk' => 'id']);
    }
}
