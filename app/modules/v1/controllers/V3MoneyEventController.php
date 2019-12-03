<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 12/2/2019
 * Time: 11:17 AM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\v3\V3MoneyEvent;
use Exception;
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
            ->where(['type' => V3MoneyEvent::type['invoice']])
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
            ->where(['type' => V3MoneyEvent::type['invoice']])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->all();
    }
}