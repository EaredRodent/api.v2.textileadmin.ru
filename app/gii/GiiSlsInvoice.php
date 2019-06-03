<?php

namespace app\gii;

use Yii;

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
 *
 * @property AnxUser $userFk
 * @property SlsMoney[] $slsMoneys
 */
class GiiSlsInvoice extends \yii\db\ActiveRecord
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
            [['ts_create', 'ts_pay'], 'safe'],
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
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['invoice_fk' => 'id']);
    }
}
