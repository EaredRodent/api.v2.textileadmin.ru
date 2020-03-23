<?php

namespace app\gii;

use app\models\AnxUser;
use Yii;

/**
 * This is the model class for table "log_error".
 *
 * @property int $id
 * @property string $ts_create
 * @property string|null $page
 * @property int|null $contact_fk
 * @property string|null $props
 * @property string|null $screenshot
 *
 * @property AnxUser $contactFk
 */
class GiiLogError extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_error';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_create'], 'safe'],
            [['contact_fk'], 'integer'],
            [['props', 'screenshot'], 'string'],
            [['page'], 'string', 'max' => 255],
            [['contact_fk'], 'exist', 'skipOnError' => true, 'targetClass' => AnxUser::className(), 'targetAttribute' => ['contact_fk' => 'id']],
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
            'page' => 'Page',
            'contact_fk' => 'Contact Fk',
            'props' => 'Props',
            'screenshot' => 'Screenshot',
        ];
    }

    /**
     * Gets query for [[ContactFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactFk()
    {
        return $this->hasOne(AnxUser::className(), ['id' => 'contact_fk']);
    }
}
