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
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsOrder;
use app\objects\Prices;
use app\objects\ProdMoveReport;


class PrStorProdController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\pr\PrStorProd';

    const actionGetReportStorIncomAll = 'GET /v1/pr-stor-prod/get-report-stor-incom-all';

    /**
     * Вернуть отчет по приходу на склад продукции из производства с разбивкой по месяцам
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetReportStorIncomAll()
    {

        $resp = [];

        $monthInfo = [
            ['num' => '01', 'str' => 'Янв'],
            ['num' => '02', 'str' => 'Фев'],
            ['num' => '03', 'str' => 'Мар'],
            ['num' => '04', 'str' => 'Апр'],
            ['num' => '05', 'str' => 'Май'],
            ['num' => '06', 'str' => 'Июн'],
            ['num' => '07', 'str' => 'Июл'],
            ['num' => '08', 'str' => 'Авг'],
            ['num' => '09', 'str' => 'Сен'],
            ['num' => '10', 'str' => 'Окт'],
            ['num' => '11', 'str' => 'Ноя'],
            ['num' => '12', 'str' => 'Дек'],
        ];

        $year = 2019;
        $month = 1;

        while (1) {
            $moveReport = new ProdMoveReport();

            $startSql = "{$year}-{$monthInfo[$month - 1]['num']}-01 00:00:00";
            $endSql = date("Y-m-t 23:59:59", strtotime($startSql));

            $items = $moveReport->getIncomProdCostCount($startSql, $endSql);
            $itemsOut = $moveReport->getOutProdCostCount($startSql, $endSql);

            $resp[] = [
                'year' => $year,
                'monthNum' => $monthInfo[$month - 1]['num'],
                'monthStr' => $monthInfo[$month - 1]['str'] . '-' . ($year - 2000),
                'data' => $items,
                'itemsOut' => $itemsOut,
            ];

            if ($year === (int)date('Y') && $month === (int)date('m')) {
                break;
            }

            $month++;
            if ($month === 13) {
                $month = 1;
                $year++;
            }
        }

        return $resp;
    }


    const actionGetReportStorIncomMonth = 'GET /v1/pr-stor-prod/get-report-stor-incom-month';

    /**
     * todo дублировние кода
     * Вернуть отчет по приходу на склад за конкретный месяц
     * @param $year - год
     * @param $month - номер месяца
     * @return array|\yii\db\ActiveRecord[]
     * @throws \Exception
     */
    public function actionGetReportStorIncomMonth($year, $month)
    {
        $prices = new Prices();

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
     * @param $year - год
     * @param $month - номер месяца
     * @return array|\yii\db\ActiveRecord[]
     * @throws \Exception
     */
    public function actionGetReportStorOutMonth($year, $month)
    {

        $prices = new Prices();

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
                ->andWhere('order_fk > 0')
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
     * @param array $groupId
     * @param array $sexId
     * @param array $classId
     * @param array $prodId
     * @param array $fabricId
     * @param array $themeId
     * @param array $printId
     * @param array $packId
     * @param null $flagInPrice
     * @param array $assortType
     * @param null $flagInProd
     * @return array
     * @throws \Exception
     */
    public function actionGetStorRests(
        array $groupId = [],
        array $sexId = [],
        array $classId = [],
        array $prodId = [],
        array $fabricId = [],
        array $themeId = [],
        array $printId = [],
        array $packId = [],
        $flagInPrice = null,
        $assortType = null,
        $flagInProd = null
    )
    {

        //$sexId = [2];
        //$classId = [1];
        //$prodId = [69, 70, 71, 72];

        $matrix = [];

        $groups = [];
        $totalCount = 0;
        $totalMoney = 0;


        // todo говнокод
        if ($flagInProd !== null) {
            $flagInProd = ($flagInProd === 'true') ? true : false;
        }
        $flagStopProd = ($flagInProd === null) ? null : (int)!$flagInProd;

        if ($flagInPrice !== null) {
            $flagInPrice = ($flagInPrice === 'true') ? true : false;
        }


        /** @var $recs PrStorProd[] */
        $recs = PrStorProd::find()
            ->select(array_merge(['{{pr_stor_prod}}.*'], Sizes::selectSumAbs))
            ->with('blankFk.modelFk.classFk.groupFk')
            ->with('blankFk.modelFk.sexFk')
            ->with('printFk')
            ->with('packFk')
            ->with('blankFk.fabricTypeFk')
            ->with('blankFk.themeFk')
            ->joinWith('blankFk.modelFk.classFk.groupFk')
            ->joinWith('blankFk.fabricTypeFk')
            ->joinWith('blankFk.themeFk')
            ->filterWhere(['ref_blank_class.group_fk' => $groupId])
            ->andFilterWhere(['ref_blank_model.sex_fk' => $sexId])
            ->andFilterWhere(['ref_blank_model.class_fk' => $classId])
            ->andFilterWhere(['blank_fk' => $prodId])
            ->andFilterWhere(['print_fk' => $printId])
            ->andFilterWhere(['pack_fk' => $packId])
            ->andFilterWhere(['ref_art_blank.fabric_type_fk' => $fabricId])
            ->andFilterWhere(['ref_art_blank.theme_fk' => $themeId])
            ->andFilterWhere(['ref_art_blank.assortment' => $assortType])
            ->andFilterWhere(['ref_art_blank.flag_stop_prod' => $flagStopProd])
            ->having('totalSum > 0')
            ->groupBy('blank_fk, print_fk, pack_fk')
            ->orderBy(
                'ref_blank_group.title, ref_blank_class.title, ref_blank_model.sex_fk, ' .
                'ref_blank_model.title, ref_fabric_type.type, ref_blank_theme.title, blank_fk, print_fk, pack_fk'
            )
            ->all();


        $prices = new Prices();

        foreach ($recs as $rec) {
            $flagInPriceVal = $prices->getFlagInPrice($rec->blank_fk, $rec->print_fk);

            // Фильтрация по flagInPrice
            if ($flagInPrice !== null) {
                if ((bool)$flagInPriceVal !== $flagInPrice) continue;
            }

            $groupName = $rec->blankFk->modelFk->classFk->groupFk->title;
            $className = $rec->blankFk->modelFk->classFk->title;
            $prodName = $rec->blankFk->hTitleForDocs($rec->printFk, $rec->packFk);

            if ($rec->print_fk > 1) {
                $assortTypeVal = 'period';
            } else {
                $assortTypeVal = $rec->blankFk->assortment;
            }
            $sizes = [];
            foreach (Sizes::fields as $fSize) {
                $sizes[$fSize] = $rec->$fSize;
                $price = round($prices->getPrice($rec->blank_fk, $rec->print_fk, $fSize) * 0.71);
                $totalMoney += $rec->$fSize * $price;
            }

            $matrix[$groupName][$className][$prodName] = [
                'pack' => $rec->packFk->title,
                'flagInPrice' => $flagInPriceVal,
                'assortType' => $assortTypeVal,
                'flagInProd' => (bool)!$rec->blankFk->flag_stop_prod,
                'sizes' => $sizes,
                'total' => (int)$rec->totalSum,
            ];
        }


        foreach ($matrix as $groupName => $classArr) {

            $classes = [];
            $sizesGroup = [
                'size_5xs' => 0,
                'size_4xs' => 0,
                'size_3xs' => 0,
                'size_2xs' => 0,
                'size_xs' => 0,
                'size_s' => 0,
                'size_m' => 0,
                'size_l' => 0,
                'size_xl' => 0,
                'size_2xl' => 0,
                'size_3xl' => 0,
                'size_4xl' => 0,
            ];
            foreach ($classArr as $className => $prodArr) {

                $prods = [];
                $sizesClass = [
                    'size_5xs' => 0,
                    'size_4xs' => 0,
                    'size_3xs' => 0,
                    'size_2xs' => 0,
                    'size_xs' => 0,
                    'size_s' => 0,
                    'size_m' => 0,
                    'size_l' => 0,
                    'size_xl' => 0,
                    'size_2xl' => 0,
                    'size_3xl' => 0,
                    'size_4xl' => 0,
                ];
                foreach ($prodArr as $prodName => $prodData) {
                    $prods[] = array_merge(['name' => $prodName], $prodData);
                    foreach (Sizes::fields as $fSize) {
                        $sizesClass[$fSize] += $prodData['sizes'][$fSize];
                        $totalCount += $prodData['sizes'][$fSize];
                    }
                }

                $classes[] = [
                    'name' => $className,
                    'sizes' => $sizesClass,
                    'total' => array_sum($sizesClass),
                    'prods' => $prods,
                ];

                foreach (Sizes::fields as $fSize) {
                    $sizesGroup[$fSize] += $sizesClass[$fSize];
                }
            }

            $groups[] = [
                'name' => $groupName,
                'sizes' => $sizesGroup,
                'total' => array_sum($sizesGroup),
                'classes' => $classes,
            ];
        }


        return [
            'groups' => $groups,
            'totalCount' => $totalCount,
            'totalMoney' => $totalMoney,
        ];
    }


    const actionGetRestTree = 'GET /v1/pr-stor-prod/get-rest-tree';

    /**
     * Вернуть остатки на складе в виде дерева
     * @throws \Exception
     */
    public function actionGetRestTree()
    {

        /** @var $recs PrStorProd[] */
        $recs = PrStorProd::find()
            ->select(array_merge(['{{pr_stor_prod}}.*'], Sizes::selectSumAbs))
            ->with([
                'blankFk.modelFk.classFk.groupFk', 'blankFk.modelFk.sexFk', 'printFk', 'packFk',
                'blankFk.fabricTypeFk', 'blankFk.themeFk'
            ])
            ->joinWith(['blankFk.modelFk.classFk.groupFk', 'blankFk.fabricTypeFk', 'blankFk.themeFk'])
            ->having('totalSum > 0')
            ->groupBy('blank_fk, print_fk, pack_fk')
            ->orderBy(
                'ref_art_blank.assortment, ref_blank_group.title, ref_blank_class.title, ' .
                'ref_blank_model.title, ref_fabric_type.type, ref_blank_theme.title, blank_fk, print_fk, pack_fk'
            )
            ->all();

        $prices = new Prices();

        // Матрица пол/ассорт/группа/наименование/[продукты со свойствами]
        $matrix = [];

        $sexData = [
            1 => 'Мужчинам',
            2 => 'Женщинам',
            3 => 'Детям',
            4 => 'Детям',
            5 => 'Женщинам',
            6 => 'Детям',
        ];

        $assortData = [
            'base' => 'Базовый ассортимент',
            'period' => 'Периодический ассортимент',
            '' => 'Не определен',
        ];

        foreach ($recs as $rec) {
            // Пол
            $sexVal = $rec->blankFk->modelFk->sex_fk;
            $sexKey = $sexData[$sexVal];

            // Ассортимент
            $assortVal = ($rec->blankFk->assortment > 1) ? 'period' : (string)$rec->blankFk->assortment;
            $assortKey = $assortData[$assortVal];

            // Группы
            $groupKey = $rec->blankFk->modelFk->classFk->groupFk->title;

            // Наименование
            $nameKey = $rec->blankFk->modelFk->classFk->title;

            // Модель

            $model = $rec->blankFk->modelFk->title;

            if (in_array($rec->blankFk->modelFk->sex_fk, [3, 4, 6])) {
                $model .= ' (' . $rec->blankFk->modelFk->sexFk->code_ru . ')';
            }

            // Скидка
            $discount = $prices->getDiscount($rec->blank_fk, $rec->print_fk);

            //Товары
            $sizesFields = ($sexKey == 'Детям') ? Sizes::fieldsRangeKids : Sizes::fieldsRangeAdult;
            $sizesVal = [];
            $totalMoney = 0;
            foreach ($sizesFields as $fSize => $strSize) {
                $sizesVal[] = ['name' => $strSize, 'count' => $rec->$fSize];
                $discountMultiplier = 1 - $discount / 100;
                $price29 = round($prices->getPrice($rec->blank_fk, $rec->print_fk, $fSize) * 0.71 * $discountMultiplier);
                $totalMoney += $rec->$fSize * $price29;
            }

            $prod = [
                'prodId' => $rec->blank_fk,
                'printId' => $rec->print_fk,
                'packId' => $rec->pack_fk,
                'art' => $rec->blankFk->hClientArt($rec->print_fk),
                'model' => $model,
                'fabric' => $rec->blankFk->fabricTypeFk->type,
                'color' => $rec->blankFk->themeFk->title,
                'print' => $rec->printFk->id === 1 ? "" : $rec->printFk->title,
                'basePrice' => $prices->getMinPrice($rec->blank_fk, $rec->print_fk),
                'flagInPrice' => $prices->getFlagInPrice($rec->blank_fk, $rec->print_fk),
                'flagInProd' => !(bool)$rec->blankFk->flag_stop_prod,
                'sizes' => $sizesVal,
                'count' => (int)$rec->totalSum,
                'price' => $totalMoney,
                'discount' => $discount
            ];

            $matrix[$sexKey][$assortKey][$groupKey][$nameKey][] = $prod;
        }


        $totalCount = 0;
        $totalPrice = 0;

        $tree = [];
        foreach ($matrix as $sexKey => $sexData) {
            $countSex = 0;
            $priceSex = 0;
            $assortArr = [];
            foreach ($sexData as $assortKey => $assortData) {
                $groupArr = [];
                $countAssort = 0;
                $priceAssort = 0;
                foreach ($assortData as $groupKey => $groupData) {
                    $classArr = [];
                    $countGroup = 0;
                    $priceGroup = 0;
                    foreach ($groupData as $classKey => $classData) {
                        $countClass = 0;
                        $priceClass = 0;
                        foreach ($classData as $prodRec) {
                            $countClass += $prodRec['count'];
                            $priceClass += $prodRec['price'];
                            $totalCount += $prodRec['count'];
                            $totalPrice += $prodRec['price'];
                        }
                        $countGroup += $countClass;
                        $priceGroup += $priceClass;
                        $classArr[] = [
                            'name' => $classKey,
                            'count' => $countClass,
                            'price' => $priceClass,
                            'prodArr' => $classData,
                        ];
                    }
                    $countAssort += $countGroup;
                    $priceAssort += $priceGroup;
                    $groupArr[] = [
                        'name' => $groupKey,
                        'count' => $countGroup,
                        'price' => $priceGroup,
                        'classArr' => $classArr,
                    ];
                }
                $countSex += $countAssort;
                $priceSex += $priceAssort;
                $assortArr[] = [
                    'name' => $assortKey,
                    'count' => $countAssort,
                    'price' => $priceAssort,
                    'groupArr' => $groupArr,
                ];
            }
            $tree[] = [
                'name' => $sexKey,
                'count' => $countSex,
                'price' => $priceSex,
                'assortArr' => $assortArr,
            ];
        }


        $resp = [
            'sexArr' => $tree,
            'count' => $totalCount,
            'price' => $totalPrice,
        ];


        return $resp;
    }


}
