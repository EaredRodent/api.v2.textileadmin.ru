<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\SlsInvoice;
use yii\db\ActiveRecord;

class SlsInvoiceController extends ActiveControllerExtended
{
    /** @var SlsInvoice $modelClass */
    public $modelClass = 'app\modules\v1\models\SlsInvoice';

    /**
     * /v1/sls-invoice/accept
     * @return array|ActiveRecord
     */
    public function actionAccept()
    {
        return $this->modelClass::find()
            ->where(['state' => $this->modelClass::stateAccept])
            ->orderBy('sort')
            ->all();
    }

    /**
     * /v1/sls-invoice/part-pay
     * @return array|ActiveRecord[]
     */
    public function actionPartPay()
    {
        return $this->modelClass::find()
            ->where(['state' => $this->modelClass::statePartPay])
            ->orderBy('sort')
            ->all();
    }

    public function actionGetWaitInvoices()
    {
        $resp = [];
        $dfdgs = [
            // АМ
            9 => 'Едуш',
            // ЕИ
            11 => 'Кривоносова',
            // Юра
            12 => 'Калашников',
            // Алена
            8 => 'Молодцова',
        ];
        foreach ($dfdgs as $key => $name) {

            $elm['name'] = $name;
            $elm['items'] = $this->modelClass::find()
                ->where(['user_fk' => $key, 'state' => $this->modelClass::stateWait])
                ->orderBy('sort')
                ->all();
            $resp[] = $elm;
        }
        return $resp;
    }

    public function actionReject($id)
    {
        $invoice = $this->modelClass::readRecord($id);
        $userId = $invoice->user_fk;

        $prevSort = $invoice->sort;

        // Новая позиция в конце отклоненных платежей

        $newSort = $this->modelClass::find()
                ->where([
                    'user_fk' => $userId,
                    'state' => $this->modelClass::stateReject
                ])
                ->count() + 1;

        $invoice->state = $this->modelClass::stateReject;
        $invoice->sort = $newSort;
        $invoice->save();

        // Сдвиг всех ожидающих платежей вверх

        $waitInvoices = $this->modelClass::readSortDown($this->modelClass::stateWait, $userId, $prevSort);
        foreach ($waitInvoices as $waitInvoice) {
            $waitInvoice->sort--;
            $waitInvoice->save();
        }
    }

    public function actionSortUp($id) {
        $invoice = $this->modelClass::readRecord($id);

        $userId = $invoice->user_fk;
        $state = $invoice->state;

        $prevSort = $invoice->sort;
        $newSort = $prevSort - 1;

        if ($prevSort > 1) {
            $upperInvoice = $this->modelClass::readSortItem($state, $userId, $newSort);
            $upperInvoice->sort = $prevSort;
            $upperInvoice->save();

            $invoice->sort = $newSort;
            $invoice->save();
        }
    }

}