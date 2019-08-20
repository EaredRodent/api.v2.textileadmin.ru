<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefBlankClass;
use Yii;

/**
 * This is the model class for table "ref_blank_group".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $title
 * @property string $title_en
 * @property string $code
 *
 * @property RefBlankClass[] $refBlankClasses
 */
class GiiRefBlankGroup extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_blank_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['title', 'code'], 'required'],
            [['title', 'title_en'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 2],
            [['code'], 'unique'],
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
            'title' => 'Title',
            'title_en' => 'Title En',
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefBlankClasses()
    {
        return $this->hasMany(RefBlankClass::className(), ['group_fk' => 'id'])->orderBy('title');
    }
}
