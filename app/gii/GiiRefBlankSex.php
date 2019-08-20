<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "ref_blank_sex".
 *
 * @property int $id
 * @property string $title
 * @property string $title_en
 * @property string $code
 * @property string $code_ru
 * @property int $bit
 *
 * @property RefBlankModel[] $refBlankModels
 */
class GiiRefBlankSex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_blank_sex';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'code', 'code_ru'], 'required'],
            [['bit'], 'integer'],
            [['title', 'title_en'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 1],
            [['code_ru'], 'string', 'max' => 3],
            [['code_ru'], 'unique'],
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
            'title' => 'Title',
            'title_en' => 'Title En',
            'code' => 'Code',
            'code_ru' => 'Code Ru',
            'bit' => 'Bit',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefBlankModels()
    {
        return $this->hasMany(RefBlankModel::className(), ['sex_fk' => 'id']);
    }
}
