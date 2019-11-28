<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/28/2019
 * Time: 2:41 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\v3\V3InvoiceType;
use Yii;
use yii\web\HttpException;

class V3InvoiceTypeController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetAll = 'GET /v1/v3-invoice-type/get-all';

    public function actionGetAll()
    {
        $invoiceTypes = V3InvoiceType::find()->all();

        return $invoiceTypes;
    }
}