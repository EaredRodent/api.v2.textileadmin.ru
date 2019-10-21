<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/11/2019
 * Time: 6:11 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsOrg;
use Yii;
use yii\web\HttpException;

class SlsOrgController extends ActiveControllerExtended
{
    /** @var SlsOrg $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsOrg';

    const actionGetOrgs = 'GET /v1/sls-org/get-orgs';

    /**
     * Получает список всех организаций
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetOrgs()
    {


        return SlsOrg::find()->all();


    }

    const actionAccept = 'POST /v1/sls-org/accept';

    /**
     * Акцептовать заявку регистрации кдиента в b2b кабинете
     * @param $id
     * @param $manager_fk
     * @param $status
     * @param $discount
     * @return array
     * @throws HttpException
     * @throws \yii\base\Exception
     */
    public function actionAccept($id, $manager_fk, $status, $discount)
    {
        $org = SlsOrg::findOne(['id' => $id]);
        if (!$org) {
            throw new HttpException(200, 'Такой организации не существует.', 200);
        }
        $org->state = 'accept';
        $org->ts_accept = date('Y-m-d H:i:s');
        $org->manager_fk = $manager_fk;
        $org->status = $status;
        $org->discount = $discount;
        if (!$org->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        $result = ['_result_' => 'success', 'accounts' => []];

        /** @var AnxUser[] $clients */
        $clients = AnxUser::findAll(['org_fk' => $org->id]);
        foreach ($clients as $client) {
            $client->status = 1;

            $password = chr(random_int(0x61, 0x7A)) . chr(random_int(0x61, 0x7A)) . chr(random_int(0x61, 0x7A));
            $password .= random_int(100, 999);
            $hash = Yii::$app->security->generatePasswordHash($password);
            $accesstoken = Yii::$app->security->generateRandomString(32);

            $client->hash = $hash;
            $client->accesstoken = $accesstoken;

            if (!$client->save()) {
                throw new HttpException(200, 'Ошибка при обновлении статуса клиента.', 200);
            }


            // temp (замена почты)
            $result['accounts'][] = [
                'login' => $client->login,
                'password' => $password
            ];
        }

        return $result;
    }


    const actionReject = 'POST /v1/sls-org/reject';

    /**
     * Отклонить заявку регистрации кдиента в b2b кабинете
     * @param $id
     * @throws HttpException
     */
    public function actionReject($id)
    {
        $org = SlsOrg::findOne(['id' => $id]);

        if (!$org) {
            throw new HttpException(200, 'Такой организации не существует.', 200);
        }
        $org->state = 'del';
        $org->ts_del = date('Y-m-d H:i:s');
        if (!$org->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        /** @var AnxUser[] $clients */
        $clients = AnxUser::findAll(['org_fk' => $org->id]);
        foreach ($clients as $client) {
            $client->status = 0;
            if (!$client->save()) {
                throw new HttpException(200, 'Ошибка при обновлении статуса клиента.', 200);
            }
        }
    }

    const actionCreateUpdate = 'POST /v1/sls-org/create-update';

    /**
     * Создает или редактирует организацию
     * @param $form
     * @return array
     * @throws HttpException
     */
    public function actionCreateUpdate($form)
    {
        $form = json_decode($form, true);

        if(isset($form['id'])) {
            $org = SlsOrg::get($form['id']);
            $org->attributes = $form;
        } else {
            $org = new SlsOrg();
            $org->attributes = $form;
            $org->state = 'wait';
        }

        if (!$org->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}