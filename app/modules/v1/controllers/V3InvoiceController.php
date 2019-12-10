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

    /**
     * Вернуть счета на подготовке (для клиента кассы)
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

        $invoices = V3Invoice::find()
            ->select(['v3_invoice.*', '(SELECT SUM(v3_money_event.summ) FROM v3_money_event WHERE v3_money_event.invoice_fk = v3_invoice.id AND v3_money_event.state != \'del\') AS sum_pay'])
            ->having('sum_pay IS NULL')
            ->andHaving(['user_fk' => $clientID])
            ->andHaving(['!=', 'flag_del', 1])
            ->all();

        return $invoices;
    }

    const actionCreateEdit = 'POST /v1/v3-invoice/create-edit';
    // Позволяет редактировать чужие счета
    const createEditAll = 'createEditAll';

    /**
     * Создать или редактировать счет
     * @param $form
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionCreateEdit($form)
    {
        $clientID = Yii::$app->getUser()->getIdentity()->getId();

        $form = json_decode($form, true);

        $invoice = null;

        if (isset($form['id'])) {
            $invoice = V3Invoice::findOne(['id' => $form['id']]);

            if (!Yii::$app->getUser()->can('createEditAll')) {
                if ($invoice->user_fk !== $clientID) {
                    throw new HttpException(200, 'Forbidden.', 200);
                }
            }
        } else {
            $invoice = new V3Invoice();
            $invoice->user_fk = $clientID;
        }

        $invoice->name = $form['name'];
        $invoice->summ = $form['summ'];
        $invoice->type_fk = $form['type_fk'];

        $invoice->save();

        return ['_result_' => 'success'];
    }

    const actionDeleteByClient = 'POST /v1/v3-invoice/delete-by-client';

    /**
     * Удалить счет (для клиента кассы)
     * @param $id
     * @throws HttpException
     */
    public function actionDeleteByClient($id)
    {
        self::deleteInvoice($id);
    }

    const actionDeleteByAdmin = 'POST /v1/v3-invoice/delete-by-admin';

    public function actionDeleteByAdmin($id)
    {
        self::deleteInvoice($id, false);
    }

    static private function deleteInvoice($id, $compareAuthorAndInitiator = true)
    {
        $invoice = V3Invoice::findOne(['id' => $id]);

        if ($compareAuthorAndInitiator) {
            $clientID = Yii::$app->getUser()->getIdentity()->getId();

            if ($invoice->user_fk !== $clientID) {
                throw new HttpException(200, 'Forbidden.', 200);
            }
        }

        $invoice->flag_del = 1;

        $invoice->save();

        return ['_result_' => 'success'];
    }

    const actionGetPrepForAdmin = 'GET /v1/v3-invoice/get-prep-for-admin';

    /**
     * Вернуть счета на подготовке (для администратора)
     * @return array
     */
    public function actionGetPrepForAdmin()
    {
        $moneyEvents = V3MoneyEvent::find()
            ->where(['direct' => V3MoneyEvent::direct['out']])
            ->andWhere(['!=', 'state', V3MoneyEvent::state['del']])
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

        /** @var V3Invoice[] $invoices */
        $invoices = V3Invoice::find()
            ->where(['id' => $invoiceWithoutMoneyEventIDs])
            ->andWhere(['flag_del' => 0])
            ->all();

        $result = [];

        foreach ($invoices as $invoice) {
            $result[$invoice->userFk->name][] = $invoice;
        }

        return $result;
    }

    const actionGetPartPayForAdmin = 'GET /v1/v3-invoice/get-part-pay-for-admin';

    /**
     * Вернуть частично опл. счета (для администратора)
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetPartPayForAdmin()
    {
        $invoices = V3Invoice::find()
            ->select(['v3_invoice.*', '(SELECT SUM(v3_money_event.summ) FROM v3_money_event WHERE v3_money_event.invoice_fk = v3_invoice.id AND v3_money_event.state != \'del\') AS sum_pay'])
            ->having('sum_pay IS NOT NULL')
            ->andHaving('-sum_pay < summ')
            ->all();

        return $invoices;
    }

    const actionGetPartPayForClient = 'GET /v1/v3-invoice/get-part-pay-for-client';

    /**
     * Вернуть частично опл. счета (для клиента кассы)
     * @param $clientID
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetPartPayForClient($clientID = 'CurrentUser')
    {
        if ((!YII_ENV_DEV) && ($clientID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($clientID === 'CurrentUser') {
            $clientID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $invoices = V3Invoice::find()
            ->select(['v3_invoice.*', '(SELECT SUM(v3_money_event.summ) FROM v3_money_event WHERE v3_money_event.invoice_fk = v3_invoice.id AND v3_money_event.state != \'del\') AS sum_pay'])
            ->having('sum_pay IS NOT NULL')
            ->andHaving('-sum_pay < summ')
            ->andHaving(['user_fk' => $clientID])
            ->all();

        return $invoices;
    }

    const actionGetFullPayForClient = 'GET /v1/v3-invoice/get-full-pay-for-client';

    /**
     * Вернуть частично опл. счета (для клиента кассы)
     * @param $date
     * @param string $clientID
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetFullPayForClient($date, $clientID = 'CurrentUser')
    {
        $dateStart = date('Y-m-1 00:00:00', strtotime($date));
        $dateEnd = date('Y-m-t 23:59:59', strtotime($date));

        if ((!YII_ENV_DEV) && ($clientID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($clientID === 'CurrentUser') {
            $clientID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $invoices = V3Invoice::find()
            ->select([
                'v3_invoice.*',
                '(SELECT SUM(v3_money_event.summ) FROM v3_money_event WHERE v3_money_event.invoice_fk = v3_invoice.id AND v3_money_event.state = \'pay\') AS sum_pay',
                '(SELECT MAX(v3_money_event.ts_pay) FROM v3_money_event WHERE v3_money_event.invoice_fk = v3_invoice.id AND v3_money_event.state = \'pay\') AS ts_pay'
            ])
            ->having('sum_pay IS NOT NULL')
            ->andHaving('-sum_pay = summ')
            ->andHaving(['user_fk' => $clientID])
            ->andHaving(['>=', 'ts_pay', $dateStart])
            ->andHaving(['<=', 'ts_pay', $dateEnd])
            ->all();

        return $invoices;
    }
}