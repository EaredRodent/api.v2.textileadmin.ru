<?php

namespace app\models;

use app\gii\GiiAnxUser;
use app\modules\AppMod;
use app\modules\v1\models\log\LogEvent;
use app\services\ServMailSend;
use Yii;
use yii\web\HttpException;

class AnxUser extends GiiAnxUser implements \yii\web\IdentityInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    public function fields()
    {
        $fields = array_merge(parent::fields(), [
            'orgFk'
        ]);


        // удаляем небезопасные поля
        unset($fields['auth_key'], $fields['url_key'], $fields['accesstoken'], $fields['hash']);

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

    public static function createPasswordForB2BContact()
    {
        $password = '';
        for ($i = 0; $i < 6; $i++) {
            $password .= random_int(0, 1) ? chr(random_int(0x61, 0x7A)) : random_int(0, 9);
        }
        return $password;
    }

    /**
     * Активация контактного лица B2B кабинета.
     * Заполнить данные пользователя необходимые для авторизации и вернуть пароль для этого пользователя
     * @param bool $urlKey
     * @return string
     * @throws \yii\base\Exception
     */
    public function fillAuthData()
    {
        $password = AnxUser::createPasswordForB2BContact();
        $hash = Yii::$app->security->generatePasswordHash($password);
        $accesstoken = Yii::$app->security->generateRandomString(32);

        $this->hash = $hash;
        $this->accesstoken = $accesstoken;

        if (!$this->url_key) {
            $url_key = Yii::$app->security->generateRandomString(16);
            $this->url_key = $url_key;
        }

        return $password;
    }

    public function sendSuccessEmail($password)
    {
        $body =
            "<p>Ваша учетная запись активирована. " .
            "Для входа в <a href='b2b.oxouno.ru'>B2B-кабинет</a> используйте:" .
            "<br>Логин: {$this->login}" .
            "<br>Пароль: <b>{$password}</b></p>";

        $result = ServMailSend::send($this->login, 'Доступ к B2B-кабинету OXOUNO', $body);

        if ($result['resp'] !== 'ok') {
            throw new HttpException(200, 'Ошибка отправки почты "' . $result['resp'] . '"', 200);
        } else {
            $logEvent = new LogEvent();
            $logEvent->event = 'sendEmailToClientAfterActivation';
            $logEvent->params = json_encode([
                'email' => $this->login,
                'anx_user.name' => $this->name,
                'sls_org.name' => $this->orgFk->name
            ], JSON_UNESCAPED_UNICODE);
            $logEvent->save();
        }
    }

    public function sendRestoreEmail($restoreLink)
    {
        $body =
            "<p>Если вы не запрашивали восстановление пароля, то не переходите по ссылке ниже.<br>Ваш пароль будет изменен.<br><br>" .
            "<a href=\"$restoreLink\">Нажмите для восстановления пароля</a></p>";

        $result = ServMailSend::send($this->login, 'B2B-кабинет OXOUNO - восстановление пароля', $body);

        if ($result['resp'] !== 'ok') {
            throw new HttpException(200, 'Ошибка отправки почты "' . $result['resp'] . '"', 200);
        }
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getProject()
    {
        return $this->project;
    }
}
