<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\Sizes;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\sls\SlsOrder;
use app\objects\Prices;
use app\objects\ProdMoveReport;


class PrStorProdController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\pr\PrStorProd';

    const actionGetReportStorIncomAll = 'GET /v1/pr-stor-prod/get-report-stor-incom-all';

    /**
     * Вернуть отчет по приходу на склад продукции из производства с разбивкой по месяцам
     * @param int $year - год по умолчанию 2019
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetReportStorIncomAll($year = 2019)
    {
        if (!$year) $year = 2019;

        $resp = [];

        $months = [
            '01' => 'Янв',
            '02' => 'Фев',
            '03' => 'Мар',
            '04' => 'Апр',
            '05' => 'Май',
            '06' => 'Июн',
            '07' => 'Июл',
            '08' => 'Авг',
            '09' => 'Сен',
            '10' => 'Окт',
            '11' => 'Ноя',
            '12' => 'Дек',
        ];

        foreach ($months as $mNum => $mStr) {

            $moveReport = new ProdMoveReport();

            $startSql = "{$year}-{$mNum}-01 00:00:00";
            $endSql = date("Y-m-t 23:59:59", strtotime($startSql));

            $items = $moveReport->getIncomProdCostCount($startSql, $endSql);
            $itemsOut = $moveReport->getOutProdCostCount($startSql, $endSql);

            $resp[] = [
                'monthNum' => $mNum,
                'monthStr' => $mStr,
                'data' => $items,
                'itemsOut' => $itemsOut,
            ];


        }

        return $resp;
    }


    const actionGetReportStorIncomMonth = 'GET /v1/pr-stor-prod/get-report-stor-incom-month';

    /**
     * todo дублировние кода
     * Вернуть отчет по приходу на склад за конкретный месяц
     * @param $month - номер месяца
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetReportStorIncomMonth($month)
    {

        $prices = new Prices();

        $year = 2019;

        $data = [];

        $startSql = "{$year}-{$month}-01 00:00:00";
        $endSql = date("Y-m-t 23:59:59", strtotime($startSql));

        //$items = PrStorProd::readRecs(['in-production', 'out-prod'], $startSql, $endSql);
        $items = PrStorProd::readRecs(['in-production'], $startSql, $endSql);
        $curBill = 0;

        foreach ($items as $item) {

            if ($curBill !== $item->waybill_fk) {
                $curBill = $item->waybill_fk;
                $data[$curBill] = [
                    'bill' => $curBill,
                    'date' => $item->dt_move,
                    'count' => 0,
                    'cost' => 0,
                ];
            }

            foreach (Sizes::fields as $fSize) {
                if ($item->$fSize > 0) {
                    $data[$curBill]['count'] += $item->$fSize;
                    $price = $prices->getPrice($item->blank_fk, $item->print_fk, $fSize);
                    $data[$curBill]['cost'] += $item->$fSize * $price * 0.71;
                }
            }

        }

        $resp = [];
        foreach ($data as $num => $item) {
            $resp[] = $item;
        }


        return $resp;
    }

    const actionGetReportStorOutMonth = 'GET /v1/pr-stor-prod/get-report-stor-out-month';

    /**
     * todo дублировние кода
     * Вернуть отчет по возврату со склада за конкретный месяц
     * @param $month - номер месяца
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetReportStorOutMonth($month)
    {

        $prices = new Prices();

        $year = 2019;

        $data = [];

        $startSql = "{$year}-{$month}-01 00:00:00";
        $endSql = date("Y-m-t 23:59:59", strtotime($startSql));

        //$items = PrStorProd::readRecs(['in-production', 'out-prod'], $startSql, $endSql);
        $items = PrStorProd::readRecs(['out-prod'], $startSql, $endSql);
        $curBill = 0;

        foreach ($items as $item) {

            if ($curBill !== $item->waybill_fk) {
                $curBill = $item->waybill_fk;
                $data[$curBill] = [
                    'bill' => $curBill,
                    'date' => $item->dt_move,
                    'count' => 0,
                    'cost' => 0,
                ];
            }

            foreach (Sizes::fields as $fSize) {
                if (abs($item->$fSize) > 0) {
                    $data[$curBill]['count'] += $item->$fSize;
                    $price = $prices->getPrice($item->blank_fk, $item->print_fk, $fSize);
                    $data[$curBill]['cost'] += $item->$fSize * $price * 0.71;
                }
            }

        }

        $resp = [];
        foreach ($data as $num => $item) {
            $resp[] = $item;
        }


        return $resp;
    }

    const actionGetReportOrderOut = 'GET /v1/pr-stor-prod/get-report-order-out';

    /**
     * Вернуть отчет помесяцам по отгрузке заказов
     */
    public function actionGetReportOrderOut()
    {
        $mounts = [
            '2017-11',
            '2017-12',
            '2018-01',
            '2018-02',
            '2018-03',
            '2018-04',
            '2018-05',
            '2018-06',
            '2018-07',
            '2018-08',
            '2018-09',
            '2018-10',
            '2018-11',
            '2018-12',
            '2019-01',
            '2019-02',
            '2019-03',
            '2019-04',
            '2019-05',
            '2019-06',
            '2019-07',
            '2019-08',
            '2019-09',
            '2019-10',
            '2019-11',
        ];

        $resp = [];

        foreach ($mounts as $mount) {
            $startSql = "$mount-01 00:00:00";
            $endSql = date("Y-m-t 23:59:59", strtotime($startSql));

            $orders = PrStorProd::find()
                ->select('order_fk')
                ->where(['direct' => 'out'])
                ->andWhere('order_fk > 0' )
                ->andWhere('dt_move >= :dateStart', [':dateStart' => $startSql])
                ->andWhere('dt_move <= :dateEnd', [':dateEnd' => $endSql])
                ->orderBy('dt_move')
                ->groupBy('order_fk')
                ->all();

            $orderIds = [];
            foreach ($orders as $order) {
                $orderIds[] = $order->order_fk;
            }


            $summ = 0;
            $orderRecs = SlsOrder::find()
                ->select('summ_order')
                ->where(['id' => $orderIds])
                ->all();
            foreach ($orderRecs as $orderRec) {
                $summ += $orderRec->summ_order;
            }



            $resp[] = [
                'month' => $mount,
                'Y' => explode('-', $mount)[0],
                'M' => explode('-', $mount)[1],
                'summ' => $summ,
                'orders' => $orderIds,
            ];
        }

        return $resp;
    }

    const actionGetStorRests = 'GET /v1/pr-stor-prod/get-stor-rests';

    /**
     * Вернуть остатки на складе по фильтрам
     */
    public function actionGetStorRests()
    {


    }


}
