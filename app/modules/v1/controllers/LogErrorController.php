<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 3/18/2020
 * Time: 12:58 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\log\LogError;
use Yii;

class LogErrorController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionLog = 'POST /v1/log-error/log';

    /**
     * Заносит в лог данные об ошибке
     * @param $page
     * @param $accesstoken
     * @param array $props
     * @param null $screenshot
     */
    public function actionLog($page, $accesstoken, array $props, $screenshot = null)
    {
        $user = AnxUser::findOne(['accesstoken' => $accesstoken]);

        $logError = new LogError();
        $logError->page = $page;
        $logError->contact_fk = $user ? $user->id : null;
        $logError->props = json_encode($props, JSON_UNESCAPED_UNICODE);
        $logError->screenshot = $screenshot;
        $logError->save();
    }

    const actionGetClientAll = 'GET /v1/log-error/get-client-all';

    /**
     * Возвращает массив ошибок на клиенте
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetClientAll()
    {
        return LogError::find()
            ->orderBy('ts_create DESC')
            ->all();
    }

    const actionGetServerAll = 'GET /v1/log-error/get-server-all';

    /**
     * Возвращает лог ошибок на сервере
     * @return array|false|string
     */
    public function actionGetServerAll()
    {
        $logFile = Yii::getAlias('@app/../runtime-web/logs/app.log');

        return file_get_contents($logFile);
    }
}