<?php

namespace app\models;

use app\gii\GiiAnxUser;
use Yii;

class AnxUser extends GiiAnxUser implements \yii\web\IdentityInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    public function fields()
    {
        $fields = parent::fields();

        // удаляем небезопасные поля
        unset($fields['auth_key'], $fields['accesstoken'], $fields['hash']);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        //return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accesstoken' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
//    public static function findByUsername($username)
//    {
//        return static::findOne(['login' => $username, 'status' => self::STATUS_ACTIVE]);
//    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
//        return $this->auth_key;
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
//        return $this->auth_key === $authKey;
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
//    public function validatePassword($password)
//    {
////        return $this->password === $password;
//        return false;
//    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getAssignments()
    {
        $resp = [];
        $usersRecs = Yii::$app->db
            ->createCommand('SELECT id, role FROM anx_user WHERE accesstoken IS NOT NULL')
            ->queryAll();
        foreach ($usersRecs as $rec) {
            $resp[$rec['id']] = [$rec['role']];
        }
        return $resp;
    }
}
