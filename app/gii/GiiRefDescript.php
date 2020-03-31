<?php

namespace app\gii;

use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProductPrint;
use Yii;

/**
 * This is the model class for table "ref_descript".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $name
 * @property string $descript
 *
 * @property RefArtBlank[] $refArtBlanks
 * @property RefProductPrint[] $refProductPrints
 */
class GiiRefDescript extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_descript';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['name', 'descript'], 'required'],
            [['descript'], 'string'],
            [['name'], 'string', 'max' => 50],
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
            'name' => 'Name',
            'descript' => 'Descript',
        ];
    }

    /**
     * Gets query for [[RefArtBlanks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefArtBlanks()
    {
        return $this->hasMany(RefArtBlank::className(), ['descript_fk' => 'id']);
    }

    /**
     * Gets query for [[RefProductPrints]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefProductPrints()
    {
        return $this->hasMany(RefProductPrint::className(), ['descript_fk' => 'id']);
    }
}
