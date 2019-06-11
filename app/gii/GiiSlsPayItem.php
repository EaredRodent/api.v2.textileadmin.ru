<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsMoney;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sls_pay_item".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $direct
 * @property string $name
 *
 * @property SlsMoney[] $slsMoneys
 */
class GiiSlsPayItem extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_pay_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['direct', 'name'], 'required'],
            [['direct'], 'string'],
            [['name'], 'string', 'max' => 200],
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
            'direct' => 'Direct',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['pay_item_fk' => 'id']);
    }
}
