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
     * @return mixed
     */
    public function actionGetPrepForAdmin()
    {
        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => V3MoneyEvent::type['invoice']])
            ->andWhere(['state' => V3MoneyEvent::state['prep']])
            ->all();
    }

    const actionGetPayForAdmin = 'GET /v1/v3-money-event/get-pay-for-admin';

    /**
     * @return mixed
     */
    public function actionGetPayForAdmin()
    {
        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['type' => [V3MoneyEvent::type['invoice'], V3MoneyEvent::type['transfer']]])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->andWhere(['>=', 'ts_pay', date('Y-m-1 00:00:00')])
            ->all();
    }

    const actionGetPrepForCashier = 'GET /v1/v3-money-event/get-prep-for-cashier';

    /**
     * @param string $cashierID
     * @return mixed
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
            ->andWhere(['type' => V3MoneyEvent::type['invoice']])
            ->andWhere(['state' => V3MoneyEvent::state['prep']])
            ->andWhere(['box_fk' => $box->id])
            ->all();
    }

    const actionSetPay = 'POST /v1/v3-money-event/set-pay';

    public function actionSetPay($id)
    {
        $cashierID = Yii::$app->getUser()->getIdentity()->getId();

        $box = V3Box::findOne(['user_fk' => $cashierID]);

        $moneyEvent = V3MoneyEvent::findOne(['id' => $id]);

        if ($moneyEvent->box_fk !== $box->id) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        $moneyEvent->state = V3MoneyEvent::state['pay'];

        $moneyEvent->save();

        return ['_result_' => 'success'];
    }

    const actionGetPayForCashier = 'GET /v1/v3-money-event/get-pay-for-cashier';

    /**
     * @param string $cashierID
     * @return mixed
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetPayForCashier($cashierID = 'CurrentUser')
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
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->andWhere(['box_fk' => $box->id])
            ->all();
    }

    const actionSetDel = 'POST /v1/v3-money-event/set-del';

    public function actionSetDel($id)
    {
        $moneyEvent = V3MoneyEvent::findOne(['id' => $id]);
        $moneyEvent->state = V3MoneyEvent::state['del'];
        $moneyEvent->save();

        return ['_result_' => 'success'];
    }

    const actionGetIncomingForAdmin = 'GET /v1/v3-money-event/get-incoming-for-admin';

    public function actionGetIncomingForAdmin()
    {
        return V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['in']])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->all();
    }
}