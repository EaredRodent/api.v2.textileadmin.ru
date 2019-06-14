<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsOrder;
use Yii;
use yii\web\HttpException;

class SlsMoneyController extends ActiveControllerExtended
{
    /** @var SlsMoney $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsMoney';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionGetOut($month = null, $userId = null, $divId = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }
        $resp = SlsMoney::getOutMoney($month, $userId, $divId);
        return $resp;
    }

    public function actionGetIncom($month = null, $clientId = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }

        $dateStart = "{$month}-01";
        $dateEnd = date("Y-m-t", strtotime($dateStart));
        $dateStartSql = date('Y-m-d 00:00:00', strtotime($dateStart));
        $dateEndSql = date('Y-m-d 23:59:59', strtotime($dateEnd));

        return SlsMoney::find()
            ->joinWith('orderFk')
            ->with('orderFk.clientFk')
            ->where(['>=', 'ts_incom', $dateStartSql])
            ->andWhere(['<=', 'ts_incom', $dateEndSql])
            ->andWhere(['direct' => SlsMoney::directIn])
            ->andFilterWhere(['sls_order.client_fk' => $clientId])
            ->orderBy('ts_incom')
            ->all();

    }

    /**
     * TODO
     */
    public function actionGetUsers()
    {
        $sql = '
        SELECT DISTINCT anx_user.name, anx_user.id
        FROM sls_money JOIN sls_invoice JOIN anx_user
        ON sls_money.invoice_fk = sls_invoice.id AND anx_user.id = sls_invoice.user_fk
        ';
        return AnxUser::findBySql($sql)->all();
    }

    public function actionMoneyOut()
    {
        $model = new SlsMoney();
        $post = Yii::$app->request->post();
        $model->invoice_fk = $post['id'];
        $model->summ = $post['cur_pay'];
        $model->ts_incom = date('Y-m-d 12:00:00', strtotime($post['ts_incom']));
        $model->pay_item_fk = $post['pay_item_fk'];
        $model->comment = isset($post['comment']) ? $post['comment'] : '';
        $model->user_fk = Yii::$app->user->getId();
        $model->direct = SlsMoney::directOut;
        $model->type = SlsMoney::typeBank;
        $model->save();

        // Сумма оплаты счета
        $invoice = SlsInvoice::get($model->invoice_fk);
        $invoice->summ_pay = bcadd($invoice->summ_pay, $model->summ);

        $dsgdg = bccomp($invoice->summ_pay, $invoice->summ);
        if ($dsgdg > 0) {
            throw new HttpException(400, 'Оплачено больше чем сумма счета');
        }

        // Счет оплачен полностью
        $dsgdsgdsgdg = bccomp($invoice->summ, $invoice->summ_pay);
        if ($dsgdsgdsgdg === 0) {
            $invoice->state = SlsInvoice::stateFullPay;
            $sortPos = SlsInvoice::getCount(SlsInvoice::stateFullPay) + 1;
            $invoice->sort = $sortPos;
            // todo удалить дырки
        }

        // Счет оплачен частично
        $dddddsaaa = bccomp($invoice->summ_pay, $invoice->summ);
        if ($dddddsaaa < 0) {
            $invoice->state = SlsInvoice::statePartPay;
            $sortPos = SlsInvoice::getCount(SlsInvoice::statePartPay) + 1;
            $invoice->sort = $sortPos;
            // todo удалить дырки
        }

        $invoice->save();
    }

    public function actionEditPay()
    {
        $post = Yii::$app->request->post();
        $model = SlsMoney::get($post['id']);
        $model->comment = $post['comment'];
        $model->pay_item_fk = $post['pay_item_fk'];
        $model->ts_incom = $post['ts_incom'];
        $model->save();
    }

    public function actionGetReport($dateStart = null, $dateEnd = null, $payType = null)
    {
        $dateMinimal = date('Y-m-d', 0);
        if (!$dateStart) {
            $dateStart = $dateMinimal;
        }
        if (!$dateEnd) {
            $dateEnd = date('Y-m-d');
        }

        /** @var SlsClient[] $clients */
        $clients = SlsClient::get(2); //->orderBy('short_name')->all();

        $ordersBeforeDateStart = SlsOrder::getForReport($dateMinimal, $dateStart, $payType);
        $moneyBeforeDateStart = SlsMoney::getForReport($dateMinimal, $dateStart, $payType);

        $result = [];

        foreach ($clients as $client) {
            $ordersSumBefore = 0;
            $moneySumBefore = 0;

            foreach ($ordersBeforeDateStart as $o) {
                if ($client->id === $o->client_fk) {
                    $ordersSumBefore += $o->summ_order;
                }
            }

            foreach ($moneyBeforeDateStart as $m) {
                if ($client->id === $m->orderFk->client_fk) {
                    $moneySumBefore += $m->summ;
                }
            }

            $result[] = [
                'client' => $client->short_name,
                'ordersSumBefore' => $ordersSumBefore,
                'moneySumBefore' => $moneySumBefore
            ];
        }

        return $result;
    }
}
