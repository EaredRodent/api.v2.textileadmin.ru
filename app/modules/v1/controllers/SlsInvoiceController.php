<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\gii\GiiSlsInvoice;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsInvoice;
use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;

class SlsInvoiceController extends ActiveControllerExtended
{
    /** @var SlsInvoice $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsInvoice';

    const actionGetAccept = 'GET /v1/sls-invoice/get-accept';

    /**
     * @return array|ActiveRecord|self[]
     */
    public function actionGetAccept()
    {
        return SlsInvoice::getAccept();
    }

    const actionGetPartPay = 'GET /v1/sls-invoice/get-part-pay';

    /**
     * @return array|ActiveRecord[]|self[]
     */
    public function actionGetPartPay()
    {
        return SlsInvoice::getPartPay();
    }

    const actionGetPartPayWithStateAccept = 'GET /v1/sls-invoice/get-part-pay-with-state-accept';

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

    const actionGetWait = 'GET /v1/sls-invoice/get-wait';

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

    const actionReject = 'POST /v1/sls-invoice/reject';

    /**
     * Отклонить счет
     * @param $id
     */
    public function actionReject($id)
    {
        $invoice = SlsInvoice::get($id);

        $userId = $invoice->user_fk;
        $prevSort = $invoice->sort;

        // Новая позиция в конце отклоненных платежей
        // (позиция сортировки не нужна, сортировать будем по дате отклонения)
        //$newSort = SlsInvoice::calcCount(SlsInvoice::stateReject, $userId) + 1;
        $invoice->state = SlsInvoice::stateReject;
        //$invoice->sort = $newSort;
        $invoice->sort = null;
        $invoice->ts_reject = date('Y-m-d H:i:s');
        $invoice->save();

        // Сдвиг всех ожидающих платежей вверх
        $waitInvoices = SlsInvoice::readSortDown(SlsInvoice::stateWait, $userId, $prevSort);
        foreach ($waitInvoices as $waitInvoice) {
            $waitInvoice->sort--;
            $waitInvoice->save();
        }
    }


    const actionSortUp = 'POST /v1/sls-invoice/sort-up';

    /**
     * Поднять счет на позицию вверх
     * @param $id
     * @throws HttpException
     */
    public function actionSortUp($id)
    {
        $invoice = SlsInvoice::get($id);

        $userId = $invoice->user_fk;

        if (Yii::$app->user->getId() !== $userId) {
            throw new HttpException(200, 'Не трогай чужой документ');
        }

        $state = $invoice->state;

        $prevSort = $invoice->sort;
        $newSort = $prevSort - 1;

        if ($prevSort > 1) {
            $upperInvoice = SlsInvoice::readSortItem($state, $userId, $newSort);
            $upperInvoice->sort = $prevSort;
            $upperInvoice->save();

            $invoice->sort = $newSort;
            $invoice->save();
        } else {
            throw new HttpException(200, 'Счет уже первый в очереди');
        }
    }


    const actionReturn = 'POST /v1/sls-invoice/return';

    /**
     * @param $id
     */
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
            $sort = SlsInvoice::calcCount(SlsInvoice::stateWait, $userId) + 1;
            $prevSort = $invoice->sort;

            $invoice->state = SlsInvoice::stateWait;
            $invoice->sort = $sort;
            $invoice->save();

            // Закрыть "дырку"
            $acceptInvoices = SlsInvoice::readSortDown(SlsInvoice::stateAccept, $userId, $prevSort);
            foreach ($acceptInvoices as $acceptInvoice) {
                $acceptInvoice->sort--;
                $acceptInvoice->save();
            }
        }
    }


    const actionAccept = 'POST /v1/sls-invoice/accept';

    /**
     * @param $id
     * @param $cur_pay
     * @param string $comment
     */
    public function actionAccept($id, $cur_pay, $comment = '')
    {
        // Позиция в сортировке
        $newSort = SlsInvoice::calcCount(SlsInvoice::stateAccept) + 1;

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
            $waitInvoices = SlsInvoice::readSortDown(SlsInvoice::stateWait, $userId, $prevSort);
            foreach ($waitInvoices as $waitInvoice) {
                $waitInvoice->sort = $waitInvoice->sort - 1;
                $waitInvoice->save();
            }
        }
    }


    const actionGetRejectInvoices = 'GET /v1/sls-invoice/get-reject-invoices';

    /**
     * Вернуть список отклоненных счетов
     */
    public function actionGetRejectInvoices()
    {
        return SlsInvoice::find()
            ->where(['state' => SlsInvoice::stateReject])
            ->orderBy('ts_reject, id')
            ->all();
    }

}