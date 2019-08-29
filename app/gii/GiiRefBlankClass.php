<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\ref\RefBlankModel;
use Yii;

/**
 * This is the model class for table "ref_blank_class".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $group_fk
 * @property string $title
 * @property string $title_client
 * @property string $title_en
 * @property string $code
 * @property string $kids_unisex
 * @property string $tag
 *
 * @property RefBlankGroup $groupFk
 * @property RefBlankModel[] $refBlankModels
 */
class GiiRefBlankClass extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_blank_class';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['group_fk', 'title', 'code'], 'required'],
            [['group_fk'], 'integer'],
            [['kids_unisex'], 'string'],
            [['title', 'title_client', 'title_en', 'tag'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 2],
            [['code'], 'unique'],
            [['group_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RefBlankGroup::className(), 'targetAttribute' => ['group_fk' => 'id']],
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
            'title' => 'Title',
            'title_client' => 'Title Client',
            'title_en' => 'Title En',
            'code' => 'Code',
            'kids_unisex' => 'Kids Unisex',
            'tag' => 'tag',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupFk()
    {
        return $this->hasOne(RefBlankGroup::className(), ['id' => 'group_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefBlankModels()
    {
        return $this->hasMany(RefBlankModel::className(), ['class_fk' => 'id'])->orderBy('title');
    }
}
