<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsInvoiceType;
use app\modules\v1\models\sls\SlsMoney;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sls_invoice".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $user_fk
 * @property int|null $type_fk
 * @property string $state
 * @property string $title
 * @property float $summ общая сумма
 * @property float|null $cur_pay Сумма текущей оплаты
 * @property float $summ_pay
 * @property int $sort
 * @property int|null $email_id
 * @property string|null $ts_pay
 * @property string|null $comment
 * @property string|null $forex валюта. если пусто - значит рубли
 * @property float|null $forex_summ
 * @property float|null $forex_summ_pay
 * @property string|null $ts_reject
 * @property int $important
 * @property string $type_pay
 *
 * @property AnxUser $userFk
 * @property SlsInvoiceType $typeFk
 * @property SlsMoney[] $slsMoneys
 */
class GiiSlsInvoice extends ActiveRecordExtended
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create', 'ts_pay', 'ts_reject'], 'safe'],
            [['user_fk', 'state', 'title', 'summ', 'sort'], 'required'],
            [['user_fk', 'type_fk', 'sort', 'email_id', 'important'], 'integer'],
            [['state', 'forex', 'type_pay'], 'string'],
            [['summ', 'cur_pay', 'summ_pay', 'forex_summ', 'forex_summ_pay'], 'number'],
            [['title'], 'string', 'max' => 250],
            [['comment'], 'string', 'max' => 255],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
            [['type_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsInvoiceType::className(), 'targetAttribute' => ['type_fk' => 'id']],
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
            'user_fk' => 'User Fk',
            'type_fk' => 'Type Fk',
            'state' => 'State',
            'title' => 'Title',
            'summ' => 'Summ',
            'cur_pay' => 'Cur Pay',
            'summ_pay' => 'Summ Pay',
            'sort' => 'Sort',
            'email_id' => 'Email ID',
            'ts_pay' => 'Ts Pay',
            'comment' => 'Comment',
            'forex' => 'Forex',
            'forex_summ' => 'Forex Summ',
            'forex_summ_pay' => 'Forex Summ Pay',
            'ts_reject' => 'Ts Reject',
            'important' => 'Important',
            'type_pay' => 'Type Pay',
        ];
    }

    /**
     * Gets query for [[UserFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }

    /**
     * Gets query for [[TypeFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeFk()
    {
        return $this->hasOne(SlsInvoiceType::className(), ['id' => 'type_fk']);
    }

    /**
     * Gets query for [[SlsMoneys]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['invoice_fk' => 'id']);
    }
}
