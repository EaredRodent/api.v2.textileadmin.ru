<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveRecordExtended;
use app\modules\v1\models\sls\SlsMoney;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "sls_invoice".
 *
 * @property int $id
 * @property string $ts_create
 * @property int $user_fk
 * @property string $state
 * @property string $title
 * @property string $summ общая сумма
 * @property string $cur_pay Сумма текущей оплаты
 * @property string $summ_pay
 * @property int $sort
 * @property int $email_id
 * @property string $ts_pay
 * @property string $comment
 * @property string $forex валюта. если пусто - значит рубли
 * @property string $forex_summ
 * @property string $forex_summ_pay
 * @property string $ts_reject
 *
 * @property AnxUser $userFk
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
            [['user_fk', 'sort', 'email_id'], 'integer'],
            [['state', 'forex'], 'string'],
            [['summ', 'cur_pay', 'summ_pay', 'forex_summ', 'forex_summ_pay'], 'number'],
            [['title'], 'string', 'max' => 250],
            [['comment'], 'string', 'max' => 255],
            [['user_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['user_fk' => 'id']],
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
            'state' => 'State',
            'title' => 'Title',
            'summ' => 'Сумма счета',
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'user_fk']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['invoice_fk' => 'id']);
    }
}
