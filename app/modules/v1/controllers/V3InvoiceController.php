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
use Yii;
use yii\web\HttpException;

class V3InvoiceController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetForClient = 'GET /v1/v3-invoice/get-for-client';

    public function actionGetForClient($clientID = 'CurrentUser')
    {
        if ((!YII_ENV_DEV) && ($clientID !== 'CurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        if ($clientID === 'CurrentUser') {
            $clientID = Yii::$app->getUser()->getIdentity()->getId();
        }

        $invoices = V3Invoice::find()
            ->where(['user_fk' => $clientID])
            ->andWhere(['state' => 'prep'])
            ->all();

        return $invoices;
    }

    const actionCreateByClient = 'POST /v1/v3-invoice/create-by-client';

    public function actionCreateByClient($form)
    {
        $clientID = Yii::$app->getUser()->getIdentity()->getId();

        $form = json_decode($form, true);

        $invoice = new V3Invoice();
        $invoice->user_fk = $clientID;
        $invoice->name = $form['name'];
        $invoice->summ = $form['summ'];
        $invoice->type_fk = $form['type_fk'];

        if(!$invoice->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}