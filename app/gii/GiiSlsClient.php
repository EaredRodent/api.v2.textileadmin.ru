<?php

namespace app\gii;

use Yii;

/**
 * This is the model class for table "sls_client".
 *
 * @property int $id
 * @property string $ts_create
 * @property string $short_name
 * @property string $inn
 * @property string $full_name
 * @property string $kpp
 * @property string $post_index
 * @property string $post_address
 * @property string $phone
 * @property string $email
 * @property string $comment
 * @property int $discount
 * @property int $manager_fk
 *
 * @property AnxUser $managerFk
 * @property SlsOrder[] $slsOrders
 */
class GiiSlsClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sls_client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['short_name', 'inn'], 'required'],
            [['discount', 'manager_fk'], 'integer'],
            [['short_name', 'inn', 'full_name', 'kpp', 'post_index', 'phone', 'email'], 'string', 'max' => 45],
            [['post_address', 'comment'], 'string', 'max' => 245],
            [['manager_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['manager_fk' => 'id']],
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
            'short_name' => 'Short Name',
            'inn' => 'Inn',
            'full_name' => 'Full Name',
            'kpp' => 'Kpp',
            'post_index' => 'Post Index',
            'post_address' => 'Post Address',
            'phone' => 'Phone',
            'email' => 'Email',
            'comment' => 'Comment',
            'discount' => 'Discount',
            'manager_fk' => 'Manager Fk',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagerFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'manager_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsOrders()
    {
        return $this->hasMany(SlsOrder::className(), ['client_fk' => 'id']);
    }
}
