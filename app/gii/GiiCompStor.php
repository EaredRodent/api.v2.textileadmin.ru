<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "comp_stor".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $ts_move
 * @property string $direct
 * @property string $type
 * @property int $item_fk
 * @property int $vendor_fk
 * @property string $count
 * @property string $price
 * @property int $worker_fk
 * @property int $part процент использования
 * @property string $comment
 *
 * @property CompItem $itemFk
 * @property CompVendor $vendorFk
 * @property CompWorker $workerFk
 */
class GiiCompStor extends \app\modules\v1\classes\ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comp_stor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_move'], 'safe'],
            [['direct', 'type', 'item_fk', 'count'], 'required'],
            [['direct', 'type'], 'string'],
            [['item_fk', 'vendor_fk', 'worker_fk', 'part'], 'integer'],
            [['count', 'price'], 'number'],
            [['comment'], 'string', 'max' => 255],
            [['item_fk'], 'exist', 'skipOnError' => true, 'targetClass' => CompItem::className(), 'targetAttribute' => ['item_fk' => 'id']],
            [['vendor_fk'], 'exist', 'skipOnError' => true, 'targetClass' => CompVendor::className(), 'targetAttribute' => ['vendor_fk' => 'id']],
            [['worker_fk'], 'exist', 'skipOnError' => true, 'targetClass' => CompWorker::className(), 'targetAttribute' => ['worker_fk' => 'id']],
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
            'ts_move' => 'Ts Move',
            'direct' => 'Direct',
            'type' => 'Type',
            'item_fk' => 'Item Fk',
            'vendor_fk' => 'Vendor Fk',
            'count' => 'Count',
            'price' => 'Price',
            'worker_fk' => 'Worker Fk',
            'part' => 'Part',
            'comment' => 'Comment',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemFk()
    {
        return $this->hasOne(CompItem::className(), ['id' => 'item_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendorFk()
    {
        return $this->hasOne(CompVendor::className(), ['id' => 'vendor_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkerFk()
    {
        return $this->hasOne(CompWorker::className(), ['id' => 'worker_fk']);
    }
}
