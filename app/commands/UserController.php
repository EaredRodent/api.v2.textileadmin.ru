<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 6/5/2020
 * Time: 11:38 AM
 */

namespace app\commands;


use app\models\AnxUser;
use Yii;
use yii\console\Controller;

class UserController extends Controller
{
    public function actionGetPassword() {
        $password = AnxUser::createPasswordForB2BContact();
        $hash = Yii::$app->security->generatePasswordHash($password);

        var_dump($password, $hash);
    }

    public function actionHashFromPassword($password) {
        echo Yii::$app->security->generatePasswordHash($password);
    }

    public function actionCmpHash($password, $hash) {
        var_dump(Yii::$app->security->validatePassword($password, $hash));
    }

    public function actionCreate($name, $login, $password, $project, $role) {
        $hash = Yii::$app->security->generatePasswordHash($password);
        $accesstoken = Yii::$app->security->generateRandomString(32);
        $url_key = Yii::$app->security->generateRandomString(16);

        $user = new AnxUser();
        $user->name = $name;
        $user->login = $login;
        $user->role = $role;
        $user->status = AnxUser::STATUS_ACTIVE;
        $user->project = $project;

        $user->hash = $hash;
        $user->accesstoken = $accesstoken;
        $user->url_key = $url_key;
        $user->auth_key = '-';

        $user->save();
    }
}