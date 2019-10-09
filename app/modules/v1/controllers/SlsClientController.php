<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\reCAPTCHA;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use yii\web\HttpException;


class SlsClientController extends ActiveControllerExtended
{
    /** @var SlsClient $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsClient';

    const actionGetForFilters = 'GET /v1/sls-client/get-for-filters';

    /**
     * Вернуть список клиентов ссортировкой по short_name
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForFilters()
    {
        return SlsClient::find()
            ->orderBy('short_name')
            ->all();
    }


    const actionRegister = "POST /v1/sls-client/register";

    /**
     * Регистрация нового клиента из B2B кабинета
     * @param $email
     * @param $brandName
     * @param $tin
     * @param $managerName
     * @param $phone
     * @param $address
     * @param null $reCaptchaToken
     * @return array
     * @throws HttpException
     */
    public function actionRegister($email, $brandName, $tin, $managerName, $phone, $address, $reCaptchaToken = null)
    {
        if (reCAPTCHA::verify($reCaptchaToken)) {
            if (!($email && $brandName && $tin && $managerName && $phone && $address)) {
                throw new HttpException(200, 'Заполните все поля.', 200);
            }

            $conflict = SlsClient::find()
                ->where(['inn' => $tin])
                ->orWhere(['email' => $email])
                ->one();

            if ($conflict) {
                throw new HttpException(200, 'Пользователь с таким ИНН или E-Mail уже зарегистрирован.', 200);
            }

            $slsClient = new SlsClient();
            $slsClient->email = $email;
            $slsClient->full_name = $brandName;
            $slsClient->inn = $tin;
            $slsClient->short_name = $managerName;
            $slsClient->phone = $phone;
            $slsClient->post_address = $address;
            $slsClient->save();
            return ['_result_' => 'success'];
        }
    }
}