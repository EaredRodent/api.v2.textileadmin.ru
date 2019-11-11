<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/11/2019
 * Time: 6:11 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsMessage;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrg;
use app\services\ServTelegramSend;
use tests\unit\models\ContactFormTest;
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

        /** @var AnxUser[] $contacts */
        $contacts = AnxUser::findAll(['org_fk' => $org->id]);
        foreach ($contacts as $contact) {
            $password = $contact->fillAuthData();
            $contact->status = 1;

            if (!$contact->save()) {
                throw new HttpException(200, 'Ошибка при обновлении статуса клиента.', 200);
            }

            $contact->sendSuccessEmail($password);
        }

        ServTelegramSend::send(AppMod::tgBotOxounoB2b, AppMod::tgGroupOxounoB2b,
            "Регистрация Клиента {$org->name} {$org->location} одобрена");

        return ['_result_' => 'success'];
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

        if (isset($form['id'])) {
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


    const actionGetForContact = 'GET /v1/sls-org/get-for-contact';

    /**
     * Возвращает организацию, в которой состоит текущее контактное лицо
     * @param string $userId [currentUser|57]
     * @return SlsOrg
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetForContact($userId = 'currentUser')
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

        $org = SlsOrg::findOne($orgId);
        if ($org) {
            return $org;
        } else {
            throw new HttpException(200, "Нет организации для \$userId = {$userId}", 200);
        }
    }

    const actionDeleteOrg = 'POST /v1/sls-org/delete-org';

    /**
     * Удаляет организацию и все ссылки на нее
     * @param $name
     * @return array
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteOrg($name)
    {
        if (!YII_ENV_DEV) {
            throw new HttpException(200, 'Работает только локально.', 200);
        }

        $org = SlsOrg::findOne(['name' => $name]);

        $messages = SlsMessage::findAll(['org_fk' => $org->id]);

        foreach ($messages as $message) {
            $message->delete();
        }

        $users = AnxUser::findAll(['org_fk' => $org->id]);

        foreach ($users as $user) {
            $orders = SlsOrder::find()
                ->where(['contact_fk' => $user->id])
                ->orWhere(['user_fk' => $user->id])
                ->all();

            foreach ($orders as $order) {
                $items = SlsItem::findAll(['order_fk' => $order->id]);

                foreach ($items as $item) {
                    $item->delete();
                }

                $order->delete();
            }

            $legalEntities = SlsClient::findAll(['manager_fk' => $user->id]);

            foreach ($legalEntities as $legalEntity) {
                $legalEntity->delete();
            }

            $user->delete();
        }

        $legalEntities = SlsClient::findAll(['org_fk' => $org->id]);

        foreach ($legalEntities as $legalEntity) {
            $orders = SlsOrder::findAll(['client_fk' => $legalEntity->id]);

            foreach ($orders as $order) {
                $items = SlsItem::findAll(['order_fk' => $order->id]);

                foreach ($items as $item) {
                    $item->delete();
                }

                $order->delete();
            }

            $legalEntity->delete();
        }

        $org->delete();

        return ['_result_' => 'success'];
    }
}