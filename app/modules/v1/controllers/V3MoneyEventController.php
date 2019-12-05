<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 12/2/2019
 * Time: 11:17 AM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\v3\V3Box;
use app\modules\v1\models\v3\V3MoneyEvent;
use Exception;
use Yii;
use yii\web\HttpException;

class V3MoneyEventController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionCreateForPrepInvoice = 'POST /v1/v3-money-event/create-for-prep-invoice';

    /**
     * Создать платеж со статусом подготовка для счета (для администратора)
     * @param $form
     * @return array
     * @throws HttpException
     */
    public function actionCreateForPrepInvoice($form)
    {
        $form = json_decode($form, true);

        $moneyEvent = new V3MoneyEvent();

        $moneyEvent->load($form, '');

        $moneyEvent->summ = -$moneyEvent->summ;

        $moneyEvent->direct = V3MoneyEvent::direct['out'];
        $moneyEvent->type = V3MoneyEvent::type['invoice'];
        $moneyEvent->state = V3MoneyEvent::state['prep'];

        $moneyEvent->save();

        return ['_result_' => 'success'];
    }

    const actionGetPrepForAdmin = 'GET /v1/v3-money-event/get-prep-for-admin';

    /**
     * Вернуть список платежей со статусом подготовка (для администратора)
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetPrepForAdmin()
    {
        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => [V3MoneyEvent::type['invoice'], V3MoneyEvent::type['transfer']]])
            ->andWhere(['state' => V3MoneyEvent::state['prep']])
            ->all();
    }

    const actionGetPayForAdmin = 'GET /v1/v3-money-event/get-pay-for-admin';

    /**
     * Вернуть список платежей со статусом оплачен (для администратора)
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetPayForAdmin($date)
    {
        $dateStart = date('Y-m-1 00:00:00', strtotime($date));
        $dateEnd = date('Y-m-t 23:59:59', strtotime($date));

        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => [V3MoneyEvent::type['invoice'], V3MoneyEvent::type['transfer']]])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->andWhere(['>=', 'ts_pay', $dateStart])
            ->andWhere(['<=', 'ts_pay', $dateEnd])
            ->all();
    }

    const actionGetPrepForCashier = 'GET /v1/v3-money-event/get-prep-for-cashier';

    /**
     * Вернуть список платежей со статусом подготовка (для кассира)
     * @param string $cashierID
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetPrepForCashier($cashierID = 'CurrentUser')
    {
        if ((!YII_ENV_DEV) && ($cashierID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($cashierID === 'CurrentUser') {
            $cashierID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $box = V3Box::findOne(['user_fk' => $cashierID]);

        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => [V3MoneyEvent::type['invoice'], V3MoneyEvent::type['transfer']]])
            ->andWhere(['state' => V3MoneyEvent::state['prep']])
            ->andWhere(['box_fk' => $box->id])
            ->all();
    }

    const actionSetPay = 'POST /v1/v3-money-event/set-pay';

    /**
     * Сменить статус платежа на оплачен (для кассира)
     * @param $id
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionSetPay($id)
    {
        $cashierID = Yii::$app->getUser()->getIdentity()->getId();

        $box = V3Box::findOne(['user_fk' => $cashierID]);

        $moneyEvent = V3MoneyEvent::findOne(['id' => $id]);

        if ($moneyEvent->box_fk !== $box->id) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        $moneyEvent->state = V3MoneyEvent::state['pay'];
        $moneyEvent->ts_pay = date('Y-m-d H:i:s');

        $moneyEvent->save();

        if ($moneyEvent->type === V3MoneyEvent::type['transfer']) {
            $moneyEventIn = new V3MoneyEvent();

            $moneyEventIn->box_fk = $moneyEvent->trans_box_fk;
            $moneyEventIn->trans_box_fk = $moneyEvent->box_fk;
            $moneyEventIn->summ = -$moneyEvent->summ;
            $moneyEventIn->direct = V3MoneyEvent::direct['in'];
            $moneyEventIn->type = V3MoneyEvent::type['transfer'];
            $moneyEventIn->state = V3MoneyEvent::state['pay'];

            $moneyEventIn->save();
        }

        return ['_result_' => 'success'];
    }

    const actionGetPayForCashier = 'GET /v1/v3-money-event/get-pay-for-cashier';

    /**
     * Вернуть список платежей со статусом оплачен (для кассира)
     * @param $date
     * @param string $cashierID
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetPayForCashier($date, $cashierID = 'CurrentUser')
    {
        $dateStart = date('Y-m-1 00:00:00', strtotime($date));
        $dateEnd = date('Y-m-t 23:59:59', strtotime($date));

        if ((!YII_ENV_DEV) && ($cashierID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($cashierID === 'CurrentUser') {
            $cashierID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $box = V3Box::findOne(['user_fk' => $cashierID]);

        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => [V3MoneyEvent::type['invoice'], V3MoneyEvent::type['transfer']]])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->andWhere(['box_fk' => $box->id])
            ->andWhere(['>=', 'ts_pay', $dateStart])
            ->andWhere(['<=', 'ts_pay', $dateEnd])
            ->all();
    }

    const actionSetDel = 'POST /v1/v3-money-event/set-del';

    /**
     * Сменить статус платежа на удален (для кассира)
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionSetDel($id)
    {
        $moneyEvent = V3MoneyEvent::findOne(['id' => $id]);
        $moneyEvent->state = V3MoneyEvent::state['del'];
        $moneyEvent->ts_del = date('Y-m-d H:i:s');
        $moneyEvent->save();

        return ['_result_' => 'success'];
    }

    const actionGetIncomingForAdmin = 'GET /v1/v3-money-event/get-incoming-for-admin';

    /**
     * Получить входящие платежи (для администратора)
     * @param $date
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetIncomingForAdmin($date)
    {
        $dateStart = date('Y-m-1 00:00:00', strtotime($date));
        $dateEnd = date('Y-m-t 23:59:59', strtotime($date));

        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['in']])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->andWhere(['>=', 'ts_pay', $dateStart])
            ->andWhere(['<=', 'ts_pay', $dateEnd])
            ->all();
    }

    const actionGetIncomingForCashier = 'GET /v1/v3-money-event/get-incoming-for-cashier';

    /**
     * Получить входящие платежи (для кассира)
     * @param $date
     * @param string $cashierID
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetIncomingForCashier($date, $cashierID = 'CurrentUser')
    {
        $dateStart = date('Y-m-1 00:00:00', strtotime($date));
        $dateEnd = date('Y-m-t 23:59:59', strtotime($date));

        if ((!YII_ENV_DEV) && ($cashierID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($cashierID === 'CurrentUser') {
            $cashierID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $box = V3Box::findOne(['user_fk' => $cashierID]);

        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['in']])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->andWhere(['box_fk' => $box->id])
            ->andWhere(['>=', 'ts_pay', $dateStart])
            ->andWhere(['<=', 'ts_pay', $dateEnd])
            ->all();
    }

    const actionTransfer = 'POST /v1/v3-money-event/transfer';

    public function actionTransfer($form)
    {
        $form = json_decode($form, true);

        $moneyEvent = new V3MoneyEvent();

        $moneyEvent->load($form, '');

        $moneyEvent->summ = -$moneyEvent->summ;

        $moneyEvent->direct = V3MoneyEvent::direct['out'];
        $moneyEvent->type = V3MoneyEvent::type['transfer'];
        $moneyEvent->state = V3MoneyEvent::state['prep'];

        $moneyEvent->save();

        return ['_result_' => 'success'];
    }

    const actionGetPrepForClient = 'GET /v1/v3-money-event/get-prep-for-client';

    /**
     * Вернуть список платежей со статусом подготовка (для клиента кассы)
     * @param string $clientID
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetPrepForClient($clientID = 'CurrentUser')
    {
        if ((!YII_ENV_DEV) && ($clientID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($clientID === 'CurrentUser') {
            $clientID = Yii::$app->getUser()->getIdentity()->getId();
        }

        return V3MoneyEvent::find()
            ->joinWith(['invoiceFk'])
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => V3MoneyEvent::type['invoice']])
            ->andWhere(['state' => V3MoneyEvent::state['prep']])
            ->andWhere(['v3_invoice.user_fk' => $clientID])
            ->all();
    }
}