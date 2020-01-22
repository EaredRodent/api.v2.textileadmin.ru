<?php

namespace app\modules\v1\models\v3;

use app\gii\GiiV3Invoice;

/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/28/2019
 * Time: 2:39 PM
 */
class V3Invoice extends GiiV3Invoice
{
    public $sum_pay;
    public $ts_pay;

    public $countEvent;

    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk',
            'typeFk',
            'countEvent',
            'sum_pay',
            'ts_pay'
        ]);
    }

    static public function getPrepForAdmin()
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

    static public function getPartPayForAdmin()
    {
        $invoices = V3Invoice::find()
            ->select(['v3_invoice.*', '(SELECT SUM(v3_money_event.summ) FROM v3_money_event WHERE v3_money_event.invoice_fk = v3_invoice.id AND v3_money_event.state != \'del\') AS sum_pay'])
            ->having('sum_pay IS NOT NULL')
            ->andHaving('-sum_pay < summ')
            ->all();

        return $invoices;
    }
}