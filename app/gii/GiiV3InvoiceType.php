<?php

namespace app\gii;

use app\modules\v1\classes\ActiveRecordExtended;
use Yii;

/**
 * This is the model class for table "v3_invoice_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property V3Invoice[] $v3Invoices
 */
class GiiV3InvoiceType extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v3_invoice_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3Invoices()
    {
        return $this->hasMany(V3Invoice::className(), ['type_fk' => 'id']);
    }
}
