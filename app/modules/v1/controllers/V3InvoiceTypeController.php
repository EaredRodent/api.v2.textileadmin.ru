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

    /**
     * Вернуть типы счета (для администратора, для клиента кассы)
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll()
    {
        $invoiceTypes = V3InvoiceType::find()->all();

        return $invoiceTypes;
    }

    const actionCreateEdit = 'POST /v1/v3-invoice-type/create-edit';

    /**
     * Создать или редактировать тип счета (для администратора)
     * @param $form
     * @return array
     * @throws HttpException
     */
    public function actionCreateEdit($form)
    {
        $form = json_decode($form, true);
        $type = null;

        if(isset($form['id'])) {
            $type = V3InvoiceType::findOne(['id' => $form['id']]);
        } else {
            $type = new V3InvoiceType();
        }

        $type->name = $form['name'];
        $type->save();

        return ['_result_' => 'success'];
    }
}