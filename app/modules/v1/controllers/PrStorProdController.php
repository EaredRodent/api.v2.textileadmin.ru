<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\ProdRest;
use app\extension\Sizes;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefCollectDiv;
use app\modules\v1\models\ref\RefCollection;
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

            // Ассортимент
            if ($rec->print_fk > 1) {
                $assortTypeVal = 'period';
            } else {
                $assortTypeVal = $rec->blankFk->assortment;
            }

            // Скидка
            $discount = $prices->getDiscount($rec->blank_fk, $rec->print_fk);

            $sizes = [];
            $totalMoneyProd = 0;
            foreach (Sizes::fields as $fSize) {
                $sizes[$fSize] = $rec->$fSize;
                $price = round($prices->getPrice($rec->blank_fk, $rec->print_fk, $fSize) * 0.71);
                $totalMoneyProd += $rec->$fSize * $price;
            }
            $totalMoney += $totalMoneyProd;

            $matrix[$groupName][$className][$prodName] = [
                'pack' => $rec->packFk->title,
                'flagInPrice' => $flagInPriceVal,
                'assortType' => $assortTypeVal,
                'flagInProd' => (bool)!$rec->blankFk->flag_stop_prod,
                'sizes' => $sizes,
                'total' => (int)$rec->totalSum,
                'totalMoney' => $totalMoneyProd,
                'minPrice' => round($prices->getMinPrice($rec->blank_fk, $rec->print_fk)),
                'discount' => $discount,
                'prodId' => $rec->blank_fk,
                'printId' => $rec->print_fk,
                'packId' => $rec->pack_fk
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
            $totalMoneyGroup = 0;

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
                $totalMoneyClass = 0;

                foreach ($prodArr as $prodName => $prodData) {
                    $prods[] = array_merge(['name' => $prodName], $prodData);
                    foreach (Sizes::fields as $fSize) {
                        $sizesClass[$fSize] += $prodData['sizes'][$fSize];
                        $totalCount += $prodData['sizes'][$fSize];
                    }
                    $totalMoneyClass += $prodData['totalMoney'];
                }

                $classes[] = [
                    'name' => $className,
                    'sizes' => $sizesClass,
                    'total' => array_sum($sizesClass),
                    'totalMoney' => $totalMoneyClass,
                    'prods' => $prods,
                ];

                foreach (Sizes::fields as $fSize) {
                    $sizesGroup[$fSize] += $sizesClass[$fSize];
                }
                $totalMoneyGroup += $totalMoneyClass;
            }

            $groups[] = [
                'name' => $groupName,
                'sizes' => $sizesGroup,
                'total' => array_sum($sizesGroup),
                'totalMoney' => $totalMoneyGroup,
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
                $price29 = round($prices->getPrice($rec->blank_fk, $rec->print_fk, $fSize) * 0.71);
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


    const actionTreeLite = 'GET /v1/pr-stor-prod/tree-lite';

    /**
     * Вызывает actionTree c fillTree = false
     * @param bool $fillTree
     */
    public function actionTreeLite($fillTree = false) {
        return $this->actionTree(false);
    }

    const actionTree = 'GET /v1/pr-stor-prod/tree';

    /**
     * Возвращает список изделий для страницы "Склад v3"
     * @param bool $fillTree
     * @return array
     */
    public function actionTree($fillTree = false)
    {
        $male = [
            'name' => 'Мужчинам',
            'ids' => [1],
            'sort' => 2
        ];

        $female = [
            'name' => 'Женщинам',
            'ids' => [2, 5],
            'sort' => 1
        ];

        $kidMale = [
            'name' => 'Мальчикам',
            'ids' => [3],
            'sort' => 4
        ];

        $kidFemale = [
            'name' => 'Девочкам',
            'ids' => [4, 6],
            'sort' => 3
        ];

        $sexTranslate = [
            1 => $male,
            2 => $female,
            3 => $kidMale,
            4 => $kidFemale,
            5 => $female,
            6 => $kidFemale,
        ];

        $sexLvlTree = [$female, $male, $kidFemale, $kidMale];

        /**
         * Сгруппировать продукт по blank_fk, print_fk и pack_fk, суммировать размеры
         * @return PrStorProd[]
         */
        function getProds()
        {
            return PrStorProd::find()
                ->select(array_merge(['{{pr_stor_prod}}.*'], Sizes::selectSumAbs))
                ->joinWith(['blankFk.modelFk.classFk.groupFk', 'blankFk.fabricTypeFk', 'blankFk.themeFk', 'blankFk.collectionFk'])
                ->having('totalSum > 0')
                ->groupBy('blank_fk, print_fk, pack_fk')
                ->all();
        }

        /**
         * Сформировать массив объектов с нужными свойствами на основе массива сгруппированных продуктов
         * @param $prods PrStorProd[]
         * @return array
         */
        function normalizeProds($prods, $sexTranslate)
        {

            $assortTranslate = [
                'base' => 'Базовый ассортимент',
                'period' => 'Периодический ассортимент',
                null => 'Не определен',
            ];

            $prices = new Prices();

            // Массив [объекты со свойствами]
            $result = [];

            foreach ($prods as $prod) {
                // Пол
                $sexValue = $prod->blankFk->modelFk->sex_fk;
                $sexText = $sexTranslate[$sexValue]['name'];
                $sexSort = $sexTranslate[$sexValue]['sort'];

                // Ассортимент
                $assortValue = $prod->blankFk->assortment;
                $assortText = $assortTranslate[$assortValue];

                // Группы
                $groupId = ' ' . $prod->blankFk->modelFk->classFk->group_fk;
                $groupText = $prod->blankFk->modelFk->classFk->groupFk->title;
                $groupSort = $prod->blankFk->modelFk->classFk->groupFk->sort;

                // Наименование
                $classId = $prod->blankFk->modelFk->class_fk;
                $classText = $prod->blankFk->modelFk->classFk->title;

                // Модель

                $modelId = ' ' . $prod->blankFk->model_fk;
                $modelText = $prod->blankFk->modelFk->fashion;
                $modelArt = $prod->blankFk->modelFk->hArt();
                $modelSort = $prod->blankFk->modelFk->sort;

                // Скидка
                $discount = $prices->getDiscount($prod->blank_fk, $prod->print_fk);

                // Колллекция
                if ($prod->print_fk === 1) {
                    $collection = $prod->blankFk->collectionFk;
                } else {
                    /** @var RefProductPrint $rpp */
                    $rpp = RefProductPrint::find()
                        ->where(['blank_fk' => $prod->blank_fk])
                        ->andWhere(['print_fk' => $prod->print_fk])
                        ->one();
                    if ($rpp) {
                        $collection = $rpp->collectionFk;
                    } else {
                        $collection = null;
                    }
                }

                $collectionId = $collection ? ' ' . $collection->id : null;
                $collectionText = $collection ? $collection->name : null;

                // Категория

                $divId = $collection ? ' ' . $collection->div_fk : null;
                $divText = $collection ? $collection->divFk->name : null;

                //Товары
                $sizesFields = ($sexText == 'Детям') ? Sizes::fieldsRangeKids : Sizes::fieldsRangeAdult;
                $sizesVal = [];
                $totalMoney = 0;
                $realSizes = [
                    'size_2xs',
                    'size_xs',
                    'size_s',
                    'size_m',
                    'size_l',
                    'size_xl',
                    'size_2xl',
                    'size_3xl',
                    'size_4xl',
                ];
                foreach ($realSizes as $fSize) {
                    $sizesVal[] = ['name' => Sizes::adults[$fSize] . ' ' . Sizes::kids[$fSize], 'count' => $prod->$fSize];

                    $price29 = round($prices->getPrice($prod->blank_fk, $prod->print_fk, $fSize) * 0.71);
                    $totalMoney += $prod->$fSize * $price29;
                }

                $prod = [
                    'prodId' => $prod->blank_fk,
                    'printId' => $prod->print_fk,
                    'packId' => $prod->pack_fk,
                    'art' => $prod->blankFk->hClientArt($prod->print_fk),
                    'fabric' => $prod->blankFk->fabricTypeFk->type,
                    'color' => $prod->blankFk->themeFk->title,
                    'print' => $prod->printFk->id === 1 ? "" : $prod->printFk->title,
                    'basePrice' => $prices->getMinPrice($prod->blank_fk, $prod->print_fk),
                    'flagInPrice' => $prices->getFlagInPrice($prod->blank_fk, $prod->print_fk),
                    'flagInProd' => !(bool)$prod->blankFk->flag_stop_prod,
                    'sizes' => $sizesVal,
                    'count' => (int)$prod->totalSum,
                    'price' => $totalMoney,
                    'discount' => $discount,
                    'sex' => $sexText,
                    'sexSort' => $sexSort,
                    'assort' => $assortText,
                    'divId' => $divId,
                    'divText' => $divText,
                    'groupId' => $groupId,
                    'group' => $groupText,
                    'groupSort' => $groupSort,
                    'class' => $classText,
                    'collectionId' => $collectionId,
                    'collection' => $collectionText,
                    'modelId' => $modelId,
                    'model' => $modelText,
                    'modelArt' => $modelArt,
                    'modelSort' => $modelSort
                ];

                $result[] = $prod;
            }

            return $result;
        }

        function makeTopTree($sexLvlTree)
        {
            /** @var RefCollectDiv[] $list1 */
            $list1 = RefCollectDiv::find()->orderBy('sort')->all();

            $tree['divArr'] = [];
            $tree['count'] = 0;
            $tree['price'] = 0;

            $cDiv = &$tree['divArr'];

            foreach ($list1 as $item1) {
                $cDiv[' ' . $item1->id]['name'] = $item1->name;
                $cDiv[' ' . $item1->id]['collectionArr'] = [];
                $cDiv[' ' . $item1->id]['prodArr'] = [];
                $cDiv[' ' . $item1->id]['count'] = 0;
                $cDiv[' ' . $item1->id]['price'] = 0;

                $collectionArr = &$cDiv[' ' . $item1->id]['collectionArr'];

                /** @var RefCollection[] $list2 */
                $list2 = RefCollection::find()->where(['div_fk' => $item1->id])->orderBy('name')->all();

                foreach ($list2 as $item2) {
                    $collectionArr[' ' . $item2->id]['name'] = $item2->name;
                    $collectionArr[' ' . $item2->id]['sexArr'] = [];
                    $collectionArr[' ' . $item2->id]['prodArr'] = [];
                    $collectionArr[' ' . $item2->id]['count'] = 0;
                    $collectionArr[' ' . $item2->id]['price'] = 0;

                    $sexArr = &$collectionArr[' ' . $item2->id]['sexArr'];

                    $list3 = $sexLvlTree;

                    foreach ($list3 as $item3) {
                        $sexArr[$item3['name']]['name'] = $item3['name'];
                        $sexArr[$item3['name']]['modelArr'] = [];
                        $sexArr[$item3['name']]['prodArr'] = [];
                        $sexArr[$item3['name']]['count'] = 0;
                        $sexArr[$item3['name']]['price'] = 0;

                        $modelArr = &$sexArr[$item3['name']]['modelArr'];

                        /** @var RefArtBlank[] $list4_1 */
                        $list4_1 = RefArtBlank::find()
                            ->joinWith(['modelFk'])
                            ->where(['collection_fk' => $item2->id])
                            ->andWhere(['in', 'ref_blank_model.sex_fk', $item3['ids']])
                            ->all();

                        /** @var RefProductPrint[] $list4_2 */
                        $list4_2 = RefProductPrint::find()
                            ->joinWith(['blankFk.modelFk'])
                            ->where(['ref_product_print.collection_fk' => $item2->id])
                            ->andWhere(['in', 'ref_blank_model.sex_fk', $item3['ids']])
                            ->all();

                        $list4 = [];

                        foreach ($list4_1 as $rab) {
                            $list4[] = $rab->modelFk;
                        }

                        foreach ($list4_2 as $rpp) {
                            $list4[] = $rpp->blankFk->modelFk;
                        }

                        usort($list4, function ($a, $b) {
                            return $a->sort - $b->sort;
                        });

                        /** @var RefBlankModel[] $list4 */
                        foreach ($list4 as $item4) {
                            if (!isset($modelArr[' ' . $item4->id])) {
                                $modelArr[' ' . $item4->id]['name'] = $item4->fashion;
                                $modelArr[' ' . $item4->id]['prodArr'] = [];
                                $modelArr[' ' . $item4->id]['count'] = 0;
                                $modelArr[' ' . $item4->id]['price'] = 0;
                            }
                        }
                    }
                }
            }

            return $tree;
        }

        function fillTopTree(&$tree, $prods)
        {
            foreach ($prods as $prod) {
                if (!$prod['divId']) {
                    continue;
                }

                $tree['count'] += $prod['count'];
                $tree['price'] += $prod['price'];

                $div = &$tree['divArr'][$prod['divId']];
                $div['prodArr'][] = $prod;
                $div['count'] += $prod['count'];
                $div['price'] += $prod['price'];

                $collection = &$div['collectionArr'][$prod['collectionId']];
                $collection['prodArr'][] = $prod;
                $collection['count'] += $prod['count'];
                $collection['price'] += $prod['price'];

                $sex = &$collection['sexArr'][$prod['sex']];
                $sex['prodArr'][] = $prod;
                $sex['count'] += $prod['count'];
                $sex['price'] += $prod['price'];

                $model = &$sex['modelArr'][$prod['modelId']];
                $model['prodArr'][] = $prod;
                $model['count'] += $prod['count'];
                $model['price'] += $prod['price'];
            }

            return $tree;
        }

        function makeBottomTree($prods)
        {
            $prods = array_filter($prods, function ($prod) {
                return $prod['divId'] === null;
            });

            $discount = array_column($prods, 'discount');
            $groupSort = array_column($prods, 'groupSort');
            $sexSort = array_column($prods, 'sexSort');
            $modelSort = array_column($prods, 'modelSort');
            array_multisort($discount, $groupSort, $sexSort, $modelSort, $prods);


            $tree['discountArr'] = [];
            $tree['count'] = 0;
            $tree['price'] = 0;

            $discountArr = &$tree['discountArr'];

            foreach ($prods as $prod) {
                if (!isset($discountArr[$prod['discount']])) {
                    $discountArr[$prod['discount']]['name'] = 'Скидка ' . $prod['discount'] . '%';
                    $discountArr[$prod['discount']]['groupArr'] = [];
                    $discountArr[$prod['discount']]['prodArr'] = [];
                    $discountArr[$prod['discount']]['count'] = 0;
                    $discountArr[$prod['discount']]['price'] = 0;
                }

                $groupArr = &$discountArr[$prod['discount']]['groupArr'];

                if (!isset($groupArr[$prod['groupId']])) {
                    $groupArr[$prod['groupId']]['name'] = $prod['group'];
                    $groupArr[$prod['groupId']]['sexArr'] = [];
                    $groupArr[$prod['groupId']]['prodArr'] = [];
                    $groupArr[$prod['groupId']]['count'] = 0;
                    $groupArr[$prod['groupId']]['price'] = 0;
                }

                $sexArr = &$groupArr[$prod['groupId']]['sexArr'];

                if (!isset($sexArr[$prod['sex']])) {
                    $sexArr[$prod['sex']]['name'] = $prod['sex'];
                    $sexArr[$prod['sex']]['modelArr'] = [];
                    $sexArr[$prod['sex']]['prodArr'] = [];
                    $sexArr[$prod['sex']]['count'] = 0;
                    $sexArr[$prod['sex']]['price'] = 0;
                }

                $modelArr = &$sexArr[$prod['sex']]['modelArr'];

                if (!isset($modelArr[$prod['modelId']])) {
                    $modelArr[$prod['modelId']]['name'] = $prod['model'];
                    $modelArr[$prod['modelId']]['prodArr'] = [];
                    $modelArr[$prod['modelId']]['count'] = 0;
                    $modelArr[$prod['modelId']]['price'] = 0;
                }
            }

            return $tree;
        }

        function fillBottomTree($tree, $prods)
        {
            $prods = array_filter($prods, function ($prod) {
                return $prod['divId'] === null;
            });

            $discountArr = &$tree['discountArr'];

            foreach ($prods as $prod) {
                $groupArr = &$discountArr[$prod['discount']]['groupArr'];
                $sexArr = &$groupArr[$prod['groupId']]['sexArr'];
                $modelArr = &$sexArr[$prod['sex']]['modelArr'];

                $prodArrFromDiscount = &$discountArr[$prod['discount']]['prodArr'];
                $prodArrFromGroup = &$groupArr[$prod['groupId']]['prodArr'];
                $prodArrFromSex = &$sexArr[$prod['sex']]['prodArr'];
                $prodArrFromModel = &$modelArr[$prod['modelId']]['prodArr'];

                $prodArrFromDiscount[] = $prod;
                $prodArrFromGroup[] = $prod;
                $prodArrFromSex[] = $prod;
                $prodArrFromModel[] = $prod;

                $tree['count'] += $prod['count'];
                $discountArr[$prod['discount']]['count'] += $prod['count'];
                $groupArr[$prod['groupId']]['count'] += $prod['count'];
                $sexArr[$prod['sex']]['count'] += $prod['count'];
                if(!isset($modelArr[$prod['modelId']]['count'])) {
                    $dddd = 10;
                }
                $modelArr[$prod['modelId']]['count'] += $prod['count'];

                $tree['price'] += $prod['price'];
                $discountArr[$prod['discount']]['price'] += $prod['price'];
                $groupArr[$prod['groupId']]['price'] += $prod['price'];
                $sexArr[$prod['sex']]['price'] += $prod['price'];
                $modelArr[$prod['modelId']]['price'] += $prod['price'];
            }

            return $tree;
        }

        $prods = getProds();
        $normalizedProds = normalizeProds($prods, $sexTranslate);
        $topTree = makeTopTree($sexLvlTree);
        $bottomTree = makeBottomTree($normalizedProds);

        if($fillTree) {
            $topTree = fillTopTree($topTree, $normalizedProds);
            $bottomTree = fillBottomTree($bottomTree, $normalizedProds);
        }

        return [
            'topTree' => $topTree,
            'bottomTree' => $bottomTree,
            'count' => $topTree['count'] + $bottomTree['count'],
            'price' => $topTree['price'] + $bottomTree['price']
        ];
    }
}
