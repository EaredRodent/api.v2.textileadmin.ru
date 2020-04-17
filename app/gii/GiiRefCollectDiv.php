<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "ref_collect_div".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $name
 * @property int $sort
 *
 * @property RefCollection[] $refCollections
 */
class GiiRefCollectDiv extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_collect_div';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['name', 'sort'], 'required'],
            [['sort'], 'integer'],
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
            'sort' => 'Sort',
        ];
    }

    /**
     * Gets query for [[RefCollections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefCollections()
    {
        return $this->hasMany(RefCollection::className(), ['div_fk' => 'id']);
    }
}
