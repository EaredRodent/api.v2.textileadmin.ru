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
    public function actionGetAccept()
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
    public function actionGetPartPay()
    {
        return $this->modelClass::find()
            ->where(['state' => $this->modelClass::statePartPay])
            ->orderBy('sort')
            ->all();
    }

    public function actionGetWait()
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
        $invoice = $this->modelClass::get($id);
        $userId = $invoice->user_fk;

        $prevSort = $invoice->sort;

        // Новая позиция в конце отклоненных платежей

        $newSort = $this->modelClass::rejectInvoicesCount($userId) + 1;

        $invoice->state = $this->modelClass::stateReject;
        $invoice->sort = $newSort;
        $invoice->save();

        // Сдвиг всех ожидающих платежей вверх

        $waitInvoices = $this->modelClass::getSortDown($this->modelClass::stateWait, $userId, $prevSort);
        foreach ($waitInvoices as $waitInvoice) {
            $waitInvoice->sort--;
            $waitInvoice->save();
        }
    }

    public function actionSortUp($id) {
        $invoice = $this->modelClass::get($id);

        $userId = $invoice->user_fk;
        $state = $invoice->state;

        $prevSort = $invoice->sort;
        $newSort = $prevSort - 1;

        if ($prevSort > 1) {
            $upperInvoice = $this->modelClass::getSortItem($state, $userId, $newSort);
            $upperInvoice->sort = $prevSort;
            $upperInvoice->save();

            $invoice->sort = $newSort;
            $invoice->save();
        }
    }

    public function actionReturn($id)
    {
        $invoice = $this->modelClass::get($id);

        if ($invoice->summ_pay > 0) {
            // Убрать в частично оплаченные
            $invoice->state = $this->modelClass::statePartPay;
            $invoice->save();
        } else {
            // Убрать в подготавливаемые
            $userId = $invoice->user_fk;
            $sort = $this->modelClass::waitInvoicesCount($userId) + 1;
            $prevSort = $invoice->sort;

            $invoice->state = $this->modelClass::stateWait;
            $invoice->sort = $sort;
            $invoice->save();

            // Закрыть "дырку"
            $acceptInvoices = $this->modelClass::getSortDown($this->modelClass::stateAccept, $userId, $prevSort);
            foreach ($acceptInvoices as $acceptInvoice) {
                $acceptInvoice->sort--;
                $acceptInvoice->save();
            }
        }
    }

    public function actionAccept($id, $cur_pay, $comment = '')
    {
        // Позиция в сортировке
        $newSort = $this->modelClass::acceptInvoicesCount() + 1;

        $invoice = $this->modelClass::get($id);

        $prevSort = $invoice->sort;
        $userId = $invoice->user_fk;

        $invoice->state = $this->modelClass::stateAccept;
        $invoice->sort = $newSort;
        $invoice->cur_pay = $cur_pay;
        $invoice->comment = $comment;
        $invoice->save();

        if ($invoice->summ_pay == 0) {
            // Закрыть "дырку" если из подготавливаемых
            $waitInvoices = $this->modelClass::getSortDown($this->modelClass::stateWait, $userId, $prevSort);
            foreach ($waitInvoices as $waitInvoice) {
                $waitInvoice->sort = $waitInvoice->sort - 1;
                $waitInvoice->save();
            }
        }
    }

}