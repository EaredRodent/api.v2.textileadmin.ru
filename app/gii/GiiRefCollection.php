<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefCollectDiv;
use Yii;

/**
 * This is the model class for table "ref_collection".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $group_fk
 * @property int $div_fk
 * @property string $name
 * @property string|null $comment
 * @property int $flag_in_price
 * @property string|null $epithets
 *
 * @property RefArtBlank[] $refArtBlanks
 * @property RefCollectDiv $divFk
 * @property RefProductPrint[] $refProductPrints
 */
class GiiRefCollection extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['group_fk', 'div_fk', 'flag_in_price'], 'integer'],
            [['name'], 'required'],
            [['epithets'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 200],
            [['div_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefCollectDiv::className(), 'targetAttribute' => ['div_fk' => 'id']],
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
            'group_fk' => 'Group Fk',
            'div_fk' => 'Div Fk',
            'name' => 'Name',
            'comment' => 'Comment',
            'flag_in_price' => 'Flag In Price',
            'epithets' => 'Epithets',
        ];
    }

    /**
     * Gets query for [[RefArtBlanks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefArtBlanks()
    {
        return $this->hasMany(RefArtBlank::className(), ['collection_fk' => 'id']);
    }

    /**
     * Gets query for [[DivFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivFk()
    {
        return $this->hasOne(RefCollectDiv::className(), ['id' => 'div_fk']);
    }

    /**
     * Gets query for [[RefProductPrints]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefProductPrints()
    {
        return $this->hasMany(RefProductPrint::className(), ['collection_fk' => 'id']);
    }
}
