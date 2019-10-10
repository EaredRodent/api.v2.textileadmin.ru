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
use app\modules\v1\classes\BaseClassTemp;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsMoney;
use ReflectionClass;
use app\services\ServMailSend;
use Yii;
use yii\web\HttpException;


class TestController extends ActiveControllerExtended
{

    public $modelClass = '';

    const actionSendMail = 'POST /v1/test/send-mail';

    /**
     * Тестирование отправки емайла
     * @param $text
     * @return array
     */
    public function actionSendMail($text)
    {
        //return ['ss' => $text];
        return ServMailSend::send($text);
    }



}