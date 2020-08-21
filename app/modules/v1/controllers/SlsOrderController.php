<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\Helper;
use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\log\LogEvent;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrderWithItems;
use app\modules\v1\models\sls\SlsOrg;
use app\services\ServTelegramSend;
use Yii;
use yii\web\HttpException;

class SlsOrderController extends ActiveControllerExtended
{
    /** @var SlsOrder $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsOrder';

    const getGetPrep = 'GET /v1/sls-order/get-prep';

    public function actionGetPrep()
    {
        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => SlsOrder::s1_prep])
            ->orderBy('ts_create')
            ->all();
    }

    const getGetInwork = 'GET /v1/sls-order/get-inwork';

    public function actionGetInwork()
    {
        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => [
                SlsOrder::s1_wait_assembl,
                SlsOrder::s5_assembl,
                SlsOrder::s2_wait,
                SlsOrder::s3_accept,
                SlsOrder::s4_reject,
                SlsOrder::s6_allow,
            ]])
            ->orderBy('ts_create')
            ->all();
    }

    const getGetSend = 'GET /v1/sls-order/get-send';

    public function actionGetSend($month = null, $clientId = null)
    {
        $month = ($month == null) ? date("Y-m") : $month;

        $dateStart = "{$month}-01";
        $dateEnd = date("Y-m-t", strtotime($dateStart));
        $dateStartSql = date('Y-m-d 00:00:00', strtotime($dateStart));
        $dateEndSql = date('Y-m-d 23:59:59', strtotime($dateEnd));

        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => SlsOrder::s7_send])
            ->andWhere(['>=', 'ts_send', $dateStartSql])
            ->andWhere(['<=', 'ts_send', $dateEndSql])
            ->andWhere(['flag_return' => 0])
            ->andFilterWhere(['client_fk' => $clientId])
            ->orderBy('ts_send')
            ->all();
    }

    const actionGetPrep2 = 'GET /v1/sls-order/get-prep-2';

    /**
     * Получает заказы на подготовке для текущего пользователя (B2B)
     * @return array|\yii\db\ActiveRecord[]
     * @throws \Throwable
     */
    public function actionGetPrep2()
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        return SlsOrderWithItems::find()
            ->where(['status' => SlsOrder::s1_client_prep])
            ->andWhere(['contact_fk' => $contact->id])
            ->all();
    }

    const actionCreateOrder = 'POST /v1/sls-order/create-order';

    /**
     * Создает заказ (B2B)
     * @param $form
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionCreateOrder($form)
    {
        $form = json_decode($form, true);

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsClient $legalEntity */
        $legalEntity = SlsClient::get($form['client_fk']);

        if (!$legalEntity) {
            throw new HttpException(200, 'Попытка добавить заказ на несуществующее юр.лицо.', 200);
        }

        if ($legalEntity->org_fk !== $contact->org_fk) {
            throw new HttpException(200, 'Попытка добавить заказ юр.лицо закрепленное за другим клиентом.', 200);
        }

        $org = SlsOrg::findOne(['id' => $contact->org_fk]);

        if (!$org) {
            throw new HttpException(200, 'Пользователь не связан с какой-либо организацией', 200);
        }

        $order = new SlsOrder();
        $order->client_fk = $form['client_fk'];
        $order->status = SlsOrder::s1_client_prep;
        $order->contact_fk = $contact->id;
        $order->user_fk = $org->manager_fk;
        date_default_timezone_set('Europe/Moscow');
        $order->ts_expire = date('Y-m-d H:i:s', time() + (60 * 60 * 1)); // На 1 час позже текщуго time()

        if (!$order->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        LogEvent::log(LogEvent::createOrder, json_encode(['id' => $order->id]));

        return ['_result_' => 'success'];
    }

    const actionGetForClient = 'GET /v1/sls-order/get-for-client';

    /**
     * Возарвщает все заказы для клиента, с которым связан текущий пользователь
     * @param null $orgFk [9 Иванов Холдинг]
     * @return array|\yii\db\ActiveRecord[]
     * @throws \Throwable
     */
    public function actionGetForClient($orgFk = null)
    {
        // todo не потестируешь на DEV
        if ($orgFk > 0 && YII_ENV_DEV) {
            // Для тестирования
            $legalEntities = SlsClient::findAll(['org_fk' => $orgFk]);
        } else {
            // Для текущего юзера
            /** @var AnxUser $contact */
            $contact = Yii::$app->getUser()->getIdentity();
            /** @var SlsClient[] $legalEntities */
            $legalEntities = SlsClient::findAll(['org_fk' => $contact->org_fk]);
        }

        $legalEntitiesIds = [];

        foreach ($legalEntities as $legalEntity) {
            $legalEntitiesIds[] = $legalEntity->id;
        }

        return SlsOrder::find()
            ->where(['client_fk' => $legalEntitiesIds])
            ->orderBy('ts_create DESC')
            ->all();
    }

    const actionGetDetails = 'GET /v1/sls-order/get-details';

    /**
     * Возарвщает все заказы для клиента, с которым связан текущий пользователь
     * @return SlsOrderWithItems
     * @throws \Throwable
     */
    public function actionGetDetails($id)
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        $order = SlsOrderWithItems::findOne(['id' => $id]);

        if ($order->clientFk->org_fk !== $contact->org_fk) {
            throw new HttpException(200, 'Попытка получить детали заказа, созданного другим клиентом.', 200);
        }

        return $order;
    }

    const actionSendOrder = 'POST /v1/sls-order/send-order';

    /**
     * Отправляет заказ на согласование (B2B)
     * @param $form
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionSendOrder($form)
    {
        $form = json_decode($form, true);

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsOrder $order */
        $order = SlsOrder::findOne(['id' => $form['active_order_id']]);

        if (!$order) {
            throw new HttpException(200, 'Попытка отправить несуществующий заказ.', 200);
        }

        if ($order->contact_fk !== $contact->id) {
            throw new HttpException(200, 'Попытка отправить заказ созданный другим контактным лицом.', 200);
        }

        $order->status = SlsOrder::s1_prep;
        $order->transp_comp = $form['transp_comp'];
        $order->addr_delivery = $form['addr_delivery'];
        $order->pact_date = $form['pact_date'];
        $order->pact_other = isset($form['pact_other']) ? $form['pact_other'] : '';

        $order->summ_order = $order->calcSummItems();

        if (!$order->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        $sumNormalize = number_format($order->summ_order, 0, '.', ' ');

        ServTelegramSend::send(AppMod::tgBotOxounoB2b, AppMod::tgGroupOxounoB2b,
            "Поступил новый заказ №{$order->id} на сумму {$sumNormalize} руб. от клиента \"{$order->clientFk->orgFk->name}\"");

        LogEvent::log(LogEvent::commitOrder, json_encode(['id' => $order->id, 'summ_order' => $order->summ_order]));

        return ['_result_' => 'success'];
    }

    const actionDeleteOrder = 'POST /v1/sls-order/delete-order';

    public function actionDeleteOrder($orderID)
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsOrder $order */
        $order = SlsOrder::findOne(['id' => $orderID]);

        if (!$order) {
            throw new HttpException(200, 'Попытка удалить несуществующий заказ.', 200);
        }

        if ($order->contact_fk !== $contact->id) {
            throw new HttpException(200, 'Попытка удалить заказ созданный другим контактным лицом.', 200);
        }

        $order->status = SlsOrder::s0_del;

        if (!$order->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

//        $slsItems = SlsItem::find()
//            ->where(['order_fk' => $order->id])
//            ->all();

//        foreach ($slsItems as $slsItem) {
//            if(!$slsItem->delete()) {
//                throw new HttpException(200, 'Внутренняя ошибка #1.', 200);
//            }
//        }
//
//        if(!$order->delete()) {
//            throw new HttpException(200, 'Внутренняя ошибка #2.', 200);
//        }

        return ['_result_' => 'success'];
    }
}
