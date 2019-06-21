<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

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

    function actionIndex()
    {
        $recs = AnxUser::find()
            ->where('accesstoken IS NOT NULL')
            ->all();
        return $recs;
    }

    function actionLogin()
    {
        $login = Yii::$app->request->post('login');
        $password = Yii::$app->request->post('password');

        /** @var $user AnxUser */
        $user = AnxUser::find()
            ->where(['login' => $login])
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

}
