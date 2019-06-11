<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsInvoice;
use yii\db\ActiveRecord;

class SlsInvoiceController extends ActiveControllerExtended
{
    /** @var SlsInvoice $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsInvoice';

    /**
     * @return array|ActiveRecord|self[]
     */
    public function actionGetAccept()
    {
        return SlsInvoice::getAccept();
    }

    /**
     * @return array|ActiveRecord[]|self[]
     */
    public function actionGetPartPay()
    {
        return SlsInvoice::getPartPay();
    }

    /**
     * @return array|ActiveRecord[]|self[]
     */
    public function actionGetPartPayWithStateAccept()
    {
        $invoices = SlsInvoice::getAccept();

        $result = [];
        foreach ($invoices as $invoice) {
            if (bccomp(bcadd($invoice->summ_pay, $invoice->cur_pay), $invoice->summ) < 0) {
                $result[] = $invoice;
            }
        }

        return $result;
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
            $elm['items'] = SlsInvoice::find()
                ->where(['user_fk' => $key, 'state' => SlsInvoice::stateWait])
                ->orderBy('sort')
                ->all();
            $resp[] = $elm;
        }
        return $resp;
    }

    public function actionReject($id)
    {
        $invoice = SlsInvoice::get($id);
        $userId = $invoice->user_fk;

        $prevSort = $invoice->sort;

        // Новая позиция в конце отклоненных платежей

        $newSort = SlsInvoice::getCount(SlsInvoice::stateReject, $userId) + 1;

        $invoice->state = SlsInvoice::stateReject;
        $invoice->sort = $newSort;
        $invoice->save();

        // Сдвиг всех ожидающих платежей вверх

        $waitInvoices = SlsInvoice::getSortDown(SlsInvoice::stateWait, $userId, $prevSort);
        foreach ($waitInvoices as $waitInvoice) {
            $waitInvoice->sort--;
            $waitInvoice->save();
        }
    }

    public function actionSortUp($id)
    {
        $invoice = SlsInvoice::get($id);

        $userId = $invoice->user_fk;
        $state = $invoice->state;

        $prevSort = $invoice->sort;
        $newSort = $prevSort - 1;

        if ($prevSort > 1) {
            $upperInvoice = SlsInvoice::getSortItem($state, $userId, $newSort);
            $upperInvoice->sort = $prevSort;
            $upperInvoice->save();

            $invoice->sort = $newSort;
            $invoice->save();
        }
    }

    public function actionReturn($id)
    {
        $invoice = SlsInvoice::get($id);

        if ($invoice->summ_pay > 0) {
            // Убрать в частично оплаченные
            $invoice->state = SlsInvoice::statePartPay;
            $invoice->save();
        } else {
            // Убрать в подготавливаемые
            $userId = $invoice->user_fk;
            $sort = SlsInvoice::getCount(SlsInvoice::stateWait, $userId) + 1;
            $prevSort = $invoice->sort;

            $invoice->state = SlsInvoice::stateWait;
            $invoice->sort = $sort;
            $invoice->save();

            // Закрыть "дырку"
            $acceptInvoices = SlsInvoice::getSortDown(SlsInvoice::stateAccept, $userId, $prevSort);
            foreach ($acceptInvoices as $acceptInvoice) {
                $acceptInvoice->sort--;
                $acceptInvoice->save();
            }
        }
    }

    public function actionAccept($id, $cur_pay, $comment = '')
    {
        // Позиция в сортировке
        $newSort = SlsInvoice::getCount(SlsInvoice::stateAccept) + 1;

        $invoice = SlsInvoice::get($id);

        $prevSort = $invoice->sort;
        $userId = $invoice->user_fk;

        $invoice->state = SlsInvoice::stateAccept;
        $invoice->sort = $newSort;
        $invoice->cur_pay = $cur_pay;
        $invoice->comment = $comment;
        $invoice->save();

        if ($invoice->summ_pay == 0) {
            // Закрыть "дырку" если из подготавливаемых
            $waitInvoices = SlsInvoice::getSortDown(SlsInvoice::stateWait, $userId, $prevSort);
            foreach ($waitInvoices as $waitInvoice) {
                $waitInvoice->sort = $waitInvoice->sort - 1;
                $waitInvoice->save();
            }
        }
    }
}