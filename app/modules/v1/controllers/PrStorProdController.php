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

            $resp[] = [
                'monthNum' => $mNum,
                'monthStr' => $mStr,
                'data' => $items,
            ];


        }

        return $resp;
    }


    const actionGetReportStorIncomMonth = 'GET /v1/pr-stor-prod/get-report-stor-incom-month';

    /**
     * Вернуть отчет по приходу на склад за конкретный месяц
     * @param $month - номер месяца
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetReportStorIncomMonth($month)
    {

        $prices = new Prices();

        $year = 2019;

        $resp = [];

        $startSql = "{$year}-{$month}-01 00:00:00";
        $endSql = date("Y-m-t 23:59:59", strtotime($startSql));

        $items = PrStorProd::readRecs(['in-production'], $startSql, $endSql);
        $curBill = 0;

        foreach ($items as $item) {

            if ($curBill !== $item->waybill_fk) {
                $curBill = $item->waybill_fk;
                $resp[$curBill] = [
                    'count' => 0,
                    'cost' => 0,
                ];
            }

            foreach (Sizes::fields as $fSize) {
                if ($item->$fSize > 0) {
                    $resp[$curBill]['count'] += $item->$fSize;
                    $price = $prices->getPrice($item->blank_fk, $item->print_fk, $fSize);
                    $resp[$curBill]['cost'] += $item->$fSize * $price;
                }
            }

        }


        return $resp;
    }


}
