<?php

namespace app\gii;

use app\models\AnxUser;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrg;
use app\modules\v1\models\sls\SlsPreorder;
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
 * @property int $org_fk ссылка на Клиента для Юр.Лица (1 Клиент может иметь 1 и более юр. лицо)
 * @property string $type_sale
 *
 * @property AnxUser $managerFk
 * @property SlsOrg $orgFk
 * @property SlsMoney[] $slsMoneys
 * @property SlsOrder[] $slsOrders
 * @property SlsPreorder[] $slsPreorders
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
            [['discount', 'manager_fk', 'org_fk'], 'integer'],
            [['type_sale'], 'string'],
            [['short_name', 'inn', 'full_name', 'kpp', 'post_index', 'phone', 'email'], 'string', 'max' => 45],
            [['post_address', 'comment'], 'string', 'max' => 245],
            [['manager_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['manager_fk' => 'id']],
            [['org_fk'], 'exist', 'skipOnError' => true, 'targetClass' => SlsOrg::className(), 'targetAttribute' => ['org_fk' => 'id']],
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
            'org_fk' => 'Org Fk',
            'type_sale' => 'Type Sale',
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
    public function getOrgFk()
    {
        return $this->hasOne(SlsOrg::className(), ['id' => 'org_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsMoneys()
    {
        return $this->hasMany(SlsMoney::className(), ['client_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsOrders()
    {
        return $this->hasMany(SlsOrder::className(), ['client_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlsPreorders()
    {
        return $this->hasMany(SlsPreorder::className(), ['client_fk' => 'id']);
    }
}
