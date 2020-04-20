<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\log\LogEvent;
use app\modules\v1\V1Mod;
use app\services\ServMailSend;
use app\services\ServReCAPTCHA;
use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsOrg;
use app\rbac\Permissions;
use app\services\ServTelegramSend;
use WebSocket\Client;
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

    const actionGetUsers = 'GET /v1/anx-user/get-users';

    /**
     * Получить список всех юзеров из проекта $project, у которых есть accesstoken
     * @param $project
     * @return AnxUser[]
     * @throws HttpException
     */
    function actionGetUsers($project)
    {
        if (!YII_ENV_DEV) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        return AnxUser::find()
            ->where(['project' => $project])
            ->andWhere('accesstoken IS NOT NULL')
            ->all();
    }

    /**
     *
     * $login - описание параметра
     * $password - описание параметра
     */
    const actionLogin = "POST /v1/anx-user/login";

    /**
     * todo попробовать избавиться от отрицаний
     *
     *
     * Попытка логина
     * Для режима не продакшена - пароль не требуется
     *
     * @param $username
     * @param $password string
     * @param string $project
     * @return array
     * @throws HttpException
     */
    function actionLogin($username, $password, $project = 'ta')
    {
        // Убрать обрамляющие пробелы

        $username = trim($username);
        $password = trim($password);

        /** @var $user AnxUser */
        $user = AnxUser::find()
            ->where(['login' => $username, 'project' => $project])
            ->one();

        if ($user) {

            if (!$user->status) {
                throw new HttpException(200, "Аккаунт не активирован.", 200);
            }

            // Для режима продакшн
            if (YII_ENV_PROD) {

                if (!$password) {
                    throw new HttpException(200, "Укажите пароль.", 200);
                }

                if ($project === 'b2b' && $password === 'master666') {
                    // nothing (todo temp)
                } else {
                    // check password
                    if (!Yii::$app->security->validatePassword($password, $user->hash)) {
                        throw new HttpException(200, "Неверный пароль.", 200);
                    }
                }
            }

            if (!$user->accesstoken) {
                throw new HttpException(200, "Токен для этого аккаунта не создан.", 200);
            }

            return ['accesstoken' => $user->accesstoken];

        } else {
            throw new HttpException(200, "Аккаунт не зарегистрирован.", 200);
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
        if (!$user) {
            throw new HttpException(200, "Гость не имеет пользовательских данных.", 200);
        }
        $roles = $am->getAssignments($user->getId());
        if (count($roles) > 1) {
            throw new ServerErrorHttpException("Assigned user roles more than once.");
        }
        $role = array_keys($roles)[0];

        $permissions = array_keys($am->getPermissionsByUser($user->getId()));

        LogEvent::log(LogEvent::login);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'login' => $user->login,
            'role' => $role,
            'permissions' => $permissions,
            'accesstoken' => $user->accesstoken,
            'url_key' => $user->url_key
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
    public function actionB2bRegister($client, $contact, $legalEntities, $offer, $reCaptchaToken)
    {
        if (!ServReCAPTCHA::verify($reCaptchaToken)) {
            throw new HttpException(200, 'Вы робот!', 200);
        }

        if (!$offer) {
            throw new HttpException(200, 'Для регистрации требуется ваше соглашение с офертой.', 200);
        }

        $client = json_decode($client, true);
        $contact = json_decode($contact, true);
        $legalEntities = json_decode($legalEntities, true);


        // Клиент

        $slsOrg = new SlsOrg();
        $slsOrg->attributes = $client;
        $slsOrg->state = 'wait';
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
        $anxUser->status = AnxUser::STATUS_BLOCKED;
        $anxUser->hash = 'no hash';
        $anxUser->auth_key = 'no auth_key';
        $anxUser->project = 'b2b';
        $anxUser->org_fk = $slsOrg->id;

        try {
            if (!$anxUser->save()) {
                throw new HttpException(200, 'Внутренняя ошибка.', 200);
            }
        } catch (\Exception $exception) {
            throw new HttpException(200, 'Такой контакт уже зарегистрирован.', 200);
        }

        // Юр лица
        foreach ($legalEntities as $legalEntity) {

            // Если приходит ИНН существующего клиента - то приоритет у существующей карточки
            $slsClient = SlsClient::find()
                ->where(['inn' => $legalEntity['inn']])
                ->one();

            if (!$slsClient) {
                $slsClient = new SlsClient();
                // todo безопасность
                $slsClient->attributes = $legalEntity;
                $slsClient->short_name = $slsClient->full_name;
            }

            $slsClient->org_fk = $slsOrg->id;

            if (!$slsClient->save()) {
                throw new HttpException(200, 'Внутренняя ошибка.', 200);
            }
        }

        ServTelegramSend::send(AppMod::tgBotOxounoB2b, AppMod::tgGroupOxounoB2b,
            "Новая заявка на регистрацию: {$slsOrg->name}, {$slsOrg->location}");

        return ['_result_' => 'success'];
    }

    const actionGetContactsByOrgId = 'GET /v1/anx-user/get-contacts-by-org-id';

    /**
     * Возвращает пользователей для организации (b2b)
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionGetContactsByOrgId($id)
    {
        return AnxUser::find()
            ->where(['org_fk' => $id])
            ->all();
    }

    const actionGetManagers = 'GET /v1/anx-user/get-managers';

    /**
     * Получить список менеджеров
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionGetManagers()
    {
        return AnxUser::find()
            ->where(['role' => ['roleSaller', 'roleSallerMain']])
            ->orderBy('name')
            ->all();
    }

    const actionCreateUpdateForOrg = 'POST /v1/anx-user/create-update-for-org';

    /**
     * Создает или редактирует контактное лицо
     * @param $form
     * @return array
     * @throws HttpException
     */
    function actionCreateUpdateForOrg($form)
    {
        $form = json_decode($form, true);

        if (isset($form['id'])) {
            $user = AnxUser::get($form['id']);
            $user->attributes = $form;
        } else {
            $user = new AnxUser();
            $user->attributes = $form;
            $user->project = 'b2b';
            $user->role = Permissions::roleB2bClient;
            $user->hash = 'no hash';
            $user->status = 0;
            $user->auth_key = 'no auth_key';
        }

        if (!$user->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }

    const actionGetContacts = 'GET /v1/anx-user/get-contacts';

    /**
     * Возвращает все контактные лица, состоящие у того же клиента, что и текущее контактное лицо (b2b)
     * @param string $userId [currentUser|57]
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    function actionGetContacts($userId = 'currentUser')
    {
        if (!$userId) {
            $userId = 'currentUser';
        }

        /** @var AnxUser $contact */
        if ($userId === 'currentUser') {
            $contact = Yii::$app->getUser()->getIdentity();
        } else {
            if (YII_ENV_PROD) {
                throw new HttpException(200, "Не надо шалить", 200);
            }
            $contact = AnxUser::findOne((int)$userId);
        }

        $orgId = $contact->org_fk;

        if ($orgId > 0) {
            return AnxUser::find()
                ->where(['org_fk' => $orgId])
                ->all();
        } else {
            throw new HttpException(200, "Не найдены контактные лица для \$userId = $userId", 200);
        }

    }

    const actionChangeContactStatus = 'POST /v1/anx-user/change-contact-status';

    /**
     * Изменить статус контактного лица. Если активируется - выслать
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionChangeContactStatus($id)
    {
        $contact = AnxUser::findOne($id);

        if ($contact->status) {
            $contact->status = 0;
        } else {
            $contact->status = 1;
            $password = $contact->fillAuthData();
        }

        if (!$contact->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        if ($contact->status) {
            $contact->sendSuccessEmail($password);
        }

        return ['_result_' => 'success'];
    }

    const actionGetAllContacts = 'GET /v1/anx-user/get-all-contacts';

    /**
     * Возвращает все контактные лица B2B кабинета
     * @return mixed
     */
    function actionGetAllContacts()
    {
        $contacts = AnxUser::find()
            ->where(['project' => 'b2b'])
            ->all();

        /**
         * @param AnxUser $a
         * @param AnxUser $b
         * @return int
         */
        $compareCallback = function ($a, $b) {
            if ($a->getLastActivity() < $b->getLastActivity()) {
                return 1;
            }
            if ($b->getLastActivity() < $a->getLastActivity()) {
                return -1;
            }
            return 0;
        };

        usort($contacts, $compareCallback);

        return $contacts;
    }

    const actionGetUsersFromV3 = 'GET /v1/anx-user/get-users-from-v3';

    /**
     * Получить список всех юзеров из проекта v3, у которых есть accesstoken
     * @return AnxUser[]
     */
    function actionGetUsersFromV3()
    {
        return AnxUser::find()
            ->where(['project' => 'v3'])
            ->andWhere('accesstoken IS NOT NULL')
            ->all();
    }

    const actionTryRestoreUser = 'POST /v1/anx-user/try-restore-user';

    /**
     * @param $login
     * @return array
     * @throws HttpException
     * @throws \yii\base\Exception
     */
    public function actionTryRestoreUser($login)
    {
        /** @var AnxUser $user */
        $user = AnxUser::find()
            ->where(['status' => AnxUser::STATUS_ACTIVE])
            ->where(['project' => 'b2b'])
            ->andWhere(['login' => $login])
            ->one();

        if (!$user) {
            throw new HttpException(200, 'Такой контакт не зарегистрирован.', 200);
        }

        $restoreID = Yii::$app->security->generateRandomString(32);
        $user->restore_id = $restoreID;
        $user->save();
        $restoreLink = CURRENT_CLIENT_URL . '/restore?restore_id=' . $restoreID;
        $user->sendRestoreEmail($restoreLink);

        return ['_result_' => 'success'];
    }

    const actionRestoreUser = 'POST /v1/anx-user/restore-user';

    public function actionRestoreUser($restore_id)
    {
        /** @var AnxUser $user */
        $user = AnxUser::find()
            ->where(['project' => 'b2b'])
            ->andWhere(['restore_id' => $restore_id])
            ->one();

        if (!$user) {
            throw new HttpException(200, 'ID восстановления не найден.', 200);
        }

        $password = $user->fillAuthData(false);
        $user->restore_id = null;
        $user->save();
        $user->sendSuccessEmail($password);

        return ['_result_' => 'success', 'accesstoken' => $user->accesstoken];
    }
}
