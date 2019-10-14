<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\services\ServReCAPTCHA;
use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsOrg;
use app\rbac\Permissions;
use app\services\ServTelegramSend;
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
                throw new HttpException(200, "Неверный пароль", 200);
            }
        } else {
            throw new HttpException(200, "Пользователь не зарегистрирован", 200);
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

    const actionB2bRegister = 'POST /v1/anx-user/b2b-register';

    /**
     * @param $client
     * @param $contact
     * @param $legalEntities
     * @return array
     * @throws HttpException
     */
    public function actionB2bRegister($client, $contact, $legalEntities, $reCaptchaToken)
    {
        if(!ServReCAPTCHA::verify($reCaptchaToken)) {
            throw new HttpException(200, 'Вы робот!', 200);
        }

        $client = json_decode($client, true);
        $contact = json_decode($contact, true);
        $legalEntities = json_decode($legalEntities, true);


        // Клиент

        $slsOrg = new SlsOrg();
        $slsOrg->attributes = $client;
        if (!$slsOrg->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }


        // Контакт

        $anxUser = AnxUser::find()
            ->where(['login' => $contact['login']])
            ->one();

        if ($anxUser) {
            throw new HttpException(200, 'Такой контакт уже зарегистрирован.', 200);
        }

        $anxUser = new AnxUser();
        $anxUser->attributes = $contact;
        $anxUser->role = Permissions::roleB2bClient;
        $anxUser->status = 0;
        $anxUser->hash = 'no hash';
        $anxUser->auth_key = 'no auth_key';
        $anxUser->project = 'b2b';
        $anxUser->org_fk = $slsOrg->id;

        if (!$anxUser->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }


        // Юр лица

        foreach ($legalEntities as $legalEntity) {
            $slsClient = SlsClient::find()
                ->where(['inn' => $legalEntity['inn']])
                ->one();

            if ($slsClient) {
                throw new HttpException(200, 'Такое юр. лицо уже зарегистрировано.', 200);
            }

            $slsClient = new SlsClient();
            $slsClient->attributes = $legalEntity;
            $slsClient->org_fk = $slsOrg->id;

            if (!$slsClient->save()) {
                throw new HttpException(200, 'Внутренняя ошибка.', 200);
            }
        }

//        ServTelegramSend::send(AppMod::tgBotOxounoB2b, AppMod::tgGroupOxounoB2b, 'Новый клиент');

        return ['_result_' => 'success'];
    }
}
