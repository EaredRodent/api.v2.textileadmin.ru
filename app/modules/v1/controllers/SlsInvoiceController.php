<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\Helper;
use app\gii\GiiSlsInvoice;
use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsInvoiceType;
use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;

class SlsInvoiceController extends ActiveControllerExtended
{
    /** @var SlsInvoice $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsInvoice';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions; // TODO: Change the autogenerated stub
    }

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
        /** @var SlsInvoiceType[] $invoiceTypes */
        $invoiceTypes = SlsInvoiceType::find()->all();

        foreach ($invoiceTypes as $invoiceType) {
            $elm['id'] = $invoiceType->id;
            $elm['name'] = $invoiceType->name;
            $elm['items'] = SlsInvoice::find()
                ->where(['type_fk' => $invoiceType->id, 'state' => SlsInvoice::stateWait])
                ->orderBy('important DESC, ts_pay')
                ->all();
            $resp[] = $elm;
        }

        $resp[] = [
            'id' => 0,
            'name' => 'Счета без категории',
            'items' => SlsInvoice::find()
                ->where(['type_fk' => null, 'state' => SlsInvoice::stateWait])
                ->orderBy('important DESC, ts_pay')
                ->all()
        ];

        return $resp;
    }

    const actionReject = 'POST /v1/sls-invoice/reject';

    /**
     * Отклонить счет
     * @param $id
     * @throws HttpException
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
        $invoice->sort = 0;
        $invoice->ts_reject = date('Y-m-d H:i:s');
        $invoice->save();

        // Сдвиг всех ожидающих платежей вверх
        $waitInvoices = SlsInvoice::readSortDown(SlsInvoice::stateWait, $userId, $prevSort);
        foreach ($waitInvoices as $waitInvoice) {
            $waitInvoice->sort--;
            $waitInvoice->save();
        }
    }


    const actionRejectUndo = 'POST /v1/sls-invoice/reject-undo';

    /**
     * Отменить отклонение счета
     * @param $id
     * @return string
     * @throws HttpException
     */
    public function actionRejectUndo($id)
    {
        $invoice = SlsInvoice::get($id);

        if (!$invoice) {
            throw new HttpException(200, 'Счет не найден', 200);
        }

        if ($invoice->state !== SlsInvoice::stateReject) {
            throw new HttpException(200, 'Счет не в статусе отклонен', 200);
        }

        $invoice->state = SlsInvoice::stateWait;
        $countInvoices = SlsInvoice::calcCount(SlsInvoice::stateWait, $invoice->user_fk);
        $invoice->sort = $countInvoices + 1;
        $invoice->ts_reject = null;
        $invoice->save();
        return 'ok';
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

        return ['_result_' => 'success'];
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

    const actionGetManagers = 'GET /v1/sls-invoice/get-managers';

    public function actionGetManagers()
    {
        return [
            [
                'id' => 9,
                'short_name' => 'Едуш'
            ],
            [
                'id' => 11,
                'short_name' => 'Кривоносова'
            ],
            [
                'id' => 12,
                'short_name' => 'Калашников'
            ],
            [
                'id' => 8,
                'short_name' => 'Молодцова'
            ]
        ];
    }

    const actionCreate = 'POST /v1/sls-invoice/create';

    /**
     * Создании счета вручную
     * @param $title
     * @param $type_fk
     * @param $summ
     * @param $important
     * @param null $ts_pay
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionCreate($title, $type_fk, $summ, $important = false, $ts_pay = null)
    {
        $invoice = new SlsInvoice();

        /** @var AnxUser $currentUser */
        $currentUser = Yii::$app->getUser()->getIdentity();

        $invoice->user_fk = $currentUser->id;
        $invoice->title = $title;
        $invoice->type_fk = $type_fk;
        $invoice->summ = $summ;
        $invoice->important = (int) $important;
        $invoice->ts_pay = $ts_pay;
        $invoice->state = SlsInvoice::stateWait;
        $invoice->sort = 0;
        $invoice->save();

        return ['_result_' => 'success'];
    }

    const actionEdit = 'POST /v1/sls-invoice/edit';

    public function actionEdit($id, $title, $type_fk, $summ, $important = false, $ts_pay = null, $cur_pay = null)
    {
        $invoice = SlsInvoice::findOne(['id' => $id]);
        $invoice->title = $title;
        $invoice->type_fk = $type_fk;
        $invoice->summ = $summ;
        $invoice->important = (int) $important;
        $invoice->ts_pay = $ts_pay;
        $invoice->cur_pay = $cur_pay;
        $invoice->save();

        return ['_result_' => 'success'];
    }

    const actionUploadFile = 'POST /v1/sls-invoice/upload-file';

    public function actionUploadFile($id)
    {
        $pathToInvoiceAttachement = Yii::getAlias(AppMod::filesRout[AppMod::filesInvoiceAttachement]);
        foreach ($_FILES as $file) {
            $translitName = Helper::strTranslitFileName($file['name']);
            $fileName = $pathToInvoiceAttachement . '/' . $id . '-' . $translitName;
            move_uploaded_file($file['tmp_name'], $fileName);
        }
    }

    const actionDeleteFile = 'POST /v1/sls-invoice/delete-file';

    public function actionDeleteFile($fileName)
    {
        $filePath = Yii::getAlias(AppMod::filesRout[AppMod::filesInvoiceAttachement]) . '/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    const actionGetAttachment = 'GET /v1/sls-invoice/get-attachment';

    public function actionGetAttachment($id)
    {

        $attachment = SlsInvoice::findOne(['id' => $id])->toArray(['attachment'])['attachment'];
        return $attachment;
    }
}