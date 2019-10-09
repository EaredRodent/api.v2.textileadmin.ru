<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\reCAPTCHA;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class AnxUserController
 * @package app\modules\v1\controllers
 */
class AnxUserController extends ActiveControllerExtended
{
    /** @var AnxUser $modelClass */
    public $modelClass = 'app\models\AnxUser';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    const actionIndex = 'GET /v1/anx-user/index';

    /**
     * Получить список всех юзеров, у которые есть accesstoken
     * @return AnxUser[]
     */
    function actionIndex()
    {
        return AnxUser::find()
            ->where('accesstoken IS NOT NULL')
            ->all();
    }

    /**
     *
     * $login - описание параметра
     * $password - описание параметра
     */
    const actionLogin = "POST /v1/anx-user/login";

    /**
     * Попытка логина
     * @param $username
     * @param $password string
     * @return array
     * @throws HttpException
     */
    function actionLogin($username, $password)
    {
        /** @var $user AnxUser */
        $user = AnxUser::find()
            ->where(['login' => $username])
            ->one();

        if ($user) {
            if (YII_ENV_DEV || Yii::$app->security->validatePassword($password, $user->hash)) {
                return ['accesstoken' => $user->accesstoken];
            } else {
                throw new HttpException(404, "Неверный пароль");
            }
        } else {
            throw new HttpException(404, "Пользователь не зарегистрирован");
        }

    }

    /**
     * Вернуть данные юзера и его роли в случае успешного логина
     */
    const actionBootstrap = "GET /v1/anx-user/bootstrap";

    function actionBootstrap()
    {
        /** @var $user AnxUser */
        $am = Yii::$app->getAuthManager();
        $user = Yii::$app->getUser()->getIdentity();
        $roles = $am->getAssignments($user->getId());
        if (count($roles) > 1) {
            throw new ServerErrorHttpException("Assigned user roles more than once.");
        }
        $role = array_keys($roles)[0];

        $permissions = array_keys($am->getPermissionsByUser($user->getId()));

        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $role,
            'permissions' => $permissions,
            'accesstoken' => $user->accesstoken
        ];
    }

    const postCreateUser = 'POST /v1/anx-user/create-user';

    /**
     * Создать нового пользователя
     * @param $login
     * @param $password
     * @param $name
     * @param $role
     * @return bool
     */
    function actionCreateUser($login, $password, $name, $role)
    {
        return true;
    }


    function actionSendInvoiceUsers()
    {
        return [
            // АМ
            9 => 'Едуш',
            // ЕИ
            11 => 'Кривоносова',
            // Юра
            12 => 'Калашников',
            // Алена
            8 => 'Молодцова',
        ];
    }

    const actionRegistration = "POST /v1/anx-user/registration";

    public function actionRegistration($username, $password, $brandName, $tin, $managerName, $phone, $address, $reCaptchaToken = null)
    {
        if (reCAPTCHA::verify($reCaptchaToken)) {
            $slsClient = new SlsClient();
            $slsClient->email = $username;
            $slsClient->full_name = $brandName;
            $slsClient->inn = $tin;
            $slsClient->short_name = $managerName;
            $slsClient->phone = $phone;
            $slsClient->post_address = $address;
            $slsClient->save();
        }
    }
}
