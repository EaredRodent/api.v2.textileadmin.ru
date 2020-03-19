<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 3/18/2020
 * Time: 12:58 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\log\LogError;
use Yii;

class LogErrorController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionLog = 'POST /v1/log-error/log';

    public function actionLog()
    {
        $logError = new LogError();
        $logError->props = json_encode(Yii::$app->getRequest()->post(), JSON_UNESCAPED_UNICODE);
        $logError->save();
    }
}