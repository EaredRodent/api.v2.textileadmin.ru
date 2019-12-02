<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/28/2019
 * Time: 2:41 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\v3\V3Invoice;
use app\modules\v1\models\v3\V3MoneyEvent;
use Yii;
use yii\web\HttpException;

class V3InvoiceController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetPrepForClient = 'GET /v1/v3-invoice/get-prep-for-client';

    public function actionGetPrepForClient($clientID = 'CurrentUser')
    {
        if ((!YII_ENV_DEV) && ($clientID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($clientID === 'CurrentUser') {
            $clientID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $invoices = V3Invoice::find()
            ->where(['user_fk' => $clientID])
            ->all();

        return $invoices;
    }

    const actionCreateByClient = 'POST /v1/v3-invoice/create-by-client';

    public function actionCreateByClient($form)
    {
        $clientID = Yii::$app->getUser()->getIdentity()->getId();

        $form = json_decode($form, true);

        $invoice = null;

        if (isset($form['id'])) {
            $invoice = V3Invoice::findOne(['id' => $form['id']]);

            if ($invoice->user_fk !== $clientID) {
                throw new HttpException(200, 'Forbidden.', 200);
            }
        } else {
            $invoice = new V3Invoice();
            $invoice->user_fk = $clientID;
        }

        $invoice->name = $form['name'];
        $invoice->summ = $form['summ'];
        $invoice->type_fk = $form['type_fk'];

        if (!$invoice->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }

    const actionDeleteByClient = 'POST /v1/v3-invoice/delete-by-client';

    public function actionDeleteByClient($id)
    {
        $clientID = Yii::$app->getUser()->getIdentity()->getId();

        $invoice = V3Invoice::findOne(['id' => $id]);

        if ($invoice->user_fk !== $clientID) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        $invoice->delete();

        return ['_result_' => 'success'];
    }

    const actionGetPrepForAdmin = 'GET /v1/v3-invoice/get-prep-for-admin';

    /**
     * @return mixed
     */
    public function actionGetPrepForAdmin()
    {
        $moneyEvents = V3MoneyEvent::find()
            ->groupBy('invoice_fk')
            ->all();

        $invoiceFromMoneyEventIDs = array_map(function ($moneyEvent) {
            return $moneyEvent->invoice_fk;
        }, $moneyEvents);

        $invoices = V3Invoice::find()->all();

        $invoiceIDs = array_map(function ($invoice) {
            return $invoice->id;
        }, $invoices);

        $invoiceWithoutMoneyEventIDs = array_diff($invoiceIDs, $invoiceFromMoneyEventIDs);

        return V3Invoice::find()
            ->where(['id' => $invoiceWithoutMoneyEventIDs])
            ->all();
    }

    const actionGetPartPayForAdmin = 'GET /v1/v3-invoice/get-part-pay-for-admin';

    /**
     * @return mixed
     */
    public function actionGetPartPayForAdmin()
    {
        $moneyEvents = V3MoneyEvent::find()
            ->groupBy('invoice_fk')
            ->all();

        $invoiceFromMoneyEventIDs = array_map(function ($moneyEvent) {
            return $moneyEvent->invoice_fk;
        }, $moneyEvents);

        $invoices = V3Invoice::find()->all();

        $invoiceIDs = array_map(function ($invoice) {
            return $invoice->id;
        }, $invoices);

        $invoiceWithoutMoneyEventIDs = array_diff($invoiceIDs, $invoiceFromMoneyEventIDs);

        return V3Invoice::find()
            ->where(['id' => $invoiceWithoutMoneyEventIDs])
            ->all();
    }
}