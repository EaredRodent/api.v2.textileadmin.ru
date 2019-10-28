<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefArtBlank;
use Yii;

/**
 * This is the model class for table "ref_blank_theme".
 *
 * @property int $id
 * @property string $dt_create
 * @property string $title
 * @property string $title_en
 * @property string $descript
 * @property string $title_price
 *
 * @property RefArtBlank[] $refArtBlanks
 */
class GiiRefBlankTheme extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_blank_theme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['title'], 'required'],
            [['title', 'title_en', 'descript'], 'string', 'max' => 245],
            [['title_price'], 'string', 'max' => 255],
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
            'title_en' => 'Title En',
            'descript' => 'Descript',
            'title_price' => 'Title Price',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefArtBlanks()
    {
        return $this->hasMany(RefArtBlank::className(), ['theme_fk' => 'id']);
    }
}
