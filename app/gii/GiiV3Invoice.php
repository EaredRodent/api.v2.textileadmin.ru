<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\v3\V3InvoiceType;
use Yii;

/**
 * This is the model class for table "v3_invoice".
 *
 * @property int $id
 * @property int $user_fk
 * @property int $type_fk
 * @property string $name
 * @property string $summ
 * @property string $state
 * @property string $ts_create
 * @property string $ts_accept
 * @property string $ts_full_pay
 * @property string $ts_del
 *
 * @property AnxUser $userFk
 * @property V3InvoiceType $typeFk
 * @property V3MoneyEvent[] $v3MoneyEvents
 */
class GiiV3Invoice extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v3_invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_fk', 'type_fk'], 'required'],
            [['user_fk', 'type_fk'], 'integer'],
            [['summ'], 'number'],
            [['state'], 'string'],
            [['ts_create', 'ts_accept', 'ts_full_pay', 'ts_del'], 'safe'],
            [['name'], 'string', 'max' => 80],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['type_fk'], 'exist', 'skipOnError' => true, 'targetClass' => V3InvoiceType::className(), 'targetAttribute' => ['type_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_fk' => 'User Fk',
            'type_fk' => 'Type Fk',
            'name' => 'Name',
            'summ' => 'Summ',
            'state' => 'State',
            'ts_create' => 'Ts Create',
            'ts_accept' => 'Ts Accept',
            'ts_full_pay' => 'Ts Full Pay',
            'ts_del' => 'Ts Del',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeFk()
    {
        return $this->hasOne(V3InvoiceType::className(), ['id' => 'type_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3MoneyEvents()
    {
        return $this->hasMany(V3MoneyEvent::className(), ['invoive_fk' => 'id']);
    }
}
