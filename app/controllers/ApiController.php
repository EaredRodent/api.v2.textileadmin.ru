<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\controllers;

use Codeception\Util\HttpCode;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class ApiController extends ActiveController
{

    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'optional' => ['options', 'index']
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['options', 'index'],
                    'allow' => true,
                    'roles' => ['?', '@'],
                ],
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function beforeAction($action)
    {
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Cookie, Authorization');
        Yii::$app->response->headers->set('Access-Control-Allow-Credentials', 'true');
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        return parent::beforeAction($action);
    }

    function actionIndex($module, $cmd)
    {
        // sls/getOrders
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        $permission = "{$module}/{$cmd}";

        if (Yii::$app->user->can($permission)) {

            return [
                ['module' => $module, 'cmd' => $cmd],
                ['title' => 'Click Me 2222', 'url' => "/page-two"],
                ['title' => 'Click Me 3333', 'url' => "/page-three"],
                ['title' => 'Click Me 4444', 'url' => "/page-three"],
            ];

        } else {
            throw new \yii\web\HttpException(401, 'Доступ запрещен');
        }


    }

}