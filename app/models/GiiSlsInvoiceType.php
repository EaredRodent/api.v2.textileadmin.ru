<?php

namespace app\models;

use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "sls_invoice_type".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $name
 * @property int $sort
 *
 * @property SlsInvoice[] $slsInvoices
 */
class GiiSlsInvoiceType extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_invoice_type';
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
     * Gets query for [[SlsInvoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlsInvoices()
    {
        return $this->hasMany(SlsInvoice::className(), ['type_fk' => 'id']);
    }
}
