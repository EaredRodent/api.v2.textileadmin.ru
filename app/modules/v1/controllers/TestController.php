<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\ProdRest;
use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\BaseClassTemp;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsMoney;
use app\objects\Prices;
use app\services\ServTelegramSend;
use ReflectionClass;
use app\services\ServMailSend;
use Yii;
use yii\web\HttpException;


class TestController extends ActiveControllerExtended
{

    public $modelClass = '';

    const actionTestCode = 'POST /v1/test/test-code';

    /**
     * Тестирование всякой хуйни
     * @param $text
     * @return array
     */
    public function actionTestCode()
    {
        $resp = [];

        $rest = new ProdRest();

        foreach (Sizes::fields as $fSize) {
            $resp[$fSize] = $rest->getAvailForOrder(63, 1, 3, $fSize);

        }

        return $resp;
    }



    const actionSendMail = 'POST /v1/test/send-mail';

    /**
     * Тестирование отправки емайла
     * @param $text
     * @return array
     */
    public function actionSendMail($text)
    {
        //return ['ss' => $text];
        $email = 'accnotfake@gmail.com';
        $subject = 'Регистрация в B2B-кабинете OXOUNO';
        $body = '';

        $body .= "<h1>Вас приветствует сервис OXOUNO B2B</h1>";
        $body .= "<h3>Ваша регистрация одобрена</h3>";
        $body .= "<p>Логин: azaza</p>";
        $body .= "<p>Пароль: bebebe</p>";
        $body .= "<hr>";
        $body .= "<p><i>Сообщение создано автоматически</i></p>";


        return ServMailSend::send($email, $text, $body);
    }

    const actionSendTelegram = 'POST /v1/test/send-telegram';

    /**
     * Тестирование отправки емайла
     * @param $text
     * @return array
     */
    public function actionSendTelegram($text)
    {
        $resp = ServTelegramSend::send(AppMod::tgBotOxounoB2b, AppMod::tgGroupOxounoB2b, $text);
        //$resp = ServTelegramSend::send(AppMod::tgBotTextileAdmin, AppMod::tgGroupOxounoB2b, $text);
        return ['resp' => $resp];
    }

    const actionObjPrices = 'GET /v1/test/obj-prices';

    /**
     * Тестирование объекта Prices
     * @param $prodId   [    144,     446]
     * @param $printId  [      1,     101]
     * @param $fSize    [size_xl, size_xl]
     * @return array    [    620,    1130]
     */
    public function actionObjPrices($prodId, $printId, $fSize)
    {
        $prices = new Prices();
        $price = $prices->getPrice($prodId, $printId, $fSize);

        return ['resp' => $price];
    }



}