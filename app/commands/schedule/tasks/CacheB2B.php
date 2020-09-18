<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 9/18/2020
 * Time: 2:19 PM
 */

namespace app\commands\schedule\tasks;


use app\extension\ProdRest;
use app\extension\Sizes;
use app\modules\v1\classes\CardProd;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefCollectDiv;
use app\modules\v1\models\ref\RefCollection;
use app\modules\v1\models\ref\RefProductPrint;
use app\objects\Prices;
use Yii;
use yii\caching\Cache;
use yii\caching\CacheInterface;

class CacheB2B
{
    public function init() {
        $tsBegin = time();

        // Get cache component
        /** @var CacheInterface $cache */
        $cache = Yii::$app->cacheB2B;

        // Cache same actions
        $cache->set('actionTreeLite', $this->getCacheForActionTreeLite());
        $cache->set('actionGetAppBarAssort', $this->getCacheForActionGetAppBarAssort());
        $cache->set('actionGetAppBarDiscount', $this->getCacheForActionGetAppBarDiscount());

        $tsEnd = time() - $tsBegin;
        echo "Cached! Time {$tsEnd}.";
    }

    /**
     * app/modules/v1/controllers/PrStorProdController.php/actionTreeLite
     * @return array
     */
    private function getCacheForActionTreeLite()
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

            $prodRest = new ProdRest();

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
                $className = $prod->blankFk->modelFk->classFk->oxouno;

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

                // CardProd construct

                /** @var RefProductPrint|RefArtBlank $cardProdParam */
                $cardProdParam = null;

                if ($prod->print_fk === 1) {
                    $cardProdParam = RefArtBlank::findOne(['id' => $prod->blank_fk]);
                } else {
                    $cardProdParam = RefProductPrint::findOne(['blank_fk' => $prod->blank_fk, 'print_fk' => $prod->print_fk]);
                }

                if (!$cardProdParam) {
                    continue;
                }

                if ($cardProdParam->flag_price !== 1) {
                    continue;
                }

                $cardProd = new CardProd($cardProdParam, $prodRest);

                // Prod normalization

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
                    'className' => $className,
                    'collectionId' => $collectionId,
                    'collection' => $collectionText,
                    'modelId' => $modelId,
                    'model' => $modelText,
                    'modelArt' => $modelArt,
                    'modelSort' => $modelSort,
                    'cardProd' => $cardProd
                ];

                $result[] = $prod;
            }

            return $result;
        }

        function makeTopTree($sexLvlTree)
        {
            /** @var RefCollectDiv[] $list1 */
            $list1 = RefCollectDiv::find()->orderBy('sort')->all();

            $id = 0;

            $tree['items'] = [];

            $cDiv = &$tree['items'];

            foreach ($list1 as $item1) {
                $cDiv[' ' . $item1->id]['name'] = $item1->name;
                $cDiv[' ' . $item1->id]['type'] = 'category';
                $cDiv[' ' . $item1->id]['id'] = $id++;
                $cDiv[' ' . $item1->id]['children'] = [];

                $collectionArr = &$cDiv[' ' . $item1->id]['children'];

                /** @var RefCollection[] $list2 */
                $list2 = RefCollection::find()->where(['div_fk' => $item1->id])->orderBy('name')->all();

                foreach ($list2 as $item2) {
                    $collectionArr[' ' . $item2->id]['name'] = $item2->name;
                    $collectionArr[' ' . $item2->id]['type'] = 'collection';
                    $collectionArr[' ' . $item2->id]['id'] = $id++;
                    $collectionArr[' ' . $item2->id]['children'] = [];
                    $collectionArr[' ' . $item2->id]['collectionDescription'] = $item2->epithets;
                    $collectionArr[' ' . $item2->id]['comment'] = $item2->comment;

                    $sexArr = &$collectionArr[' ' . $item2->id]['children'];

                    $list3 = $sexLvlTree;

                    foreach ($list3 as $item3) {
                        $sexArr[$item3['name']]['name'] = $item3['name'];
                        $sexArr[$item3['name']]['type'] = 'sex';
                        $sexArr[$item3['name']]['id'] = $id++;
                        $sexArr[$item3['name']]['children'] = [];

                        $modelArr = &$sexArr[$item3['name']]['children'];

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
                                $modelArr[' ' . $item4->id]['type'] = 'model';
                                $modelArr[' ' . $item4->id]['id'] = $id++;
                                $modelArr[' ' . $item4->id]['className'] = $item4->classFk->oxouno;
                                $modelArr[' ' . $item4->id]['prodArr'] = [];
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

                $div = &$tree['items'][$prod['divId']];
                $collection = &$div['children'][$prod['collectionId']];
                $sex = &$collection['children'][$prod['sex']];
                $model = &$sex['children'][$prod['modelId']];
                $model['prodArr'][] = $prod['cardProd'];
            }

            return $tree;
        }

        function restructItems($items)
        {
            foreach ($items as &$item) {
                if (isset($item['children'])) {
                    $item['children'] = restructItems($item['children']);
                }
            }
            return array_values($items);
        }

        function restructTree(&$tree)
        {
            $tree['items'] = restructItems($tree['items']);
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

            $id = 0;

            $tree['items'] = [];

            $discountArr = &$tree['items'];

            foreach ($prods as $prod) {
                if (!isset($discountArr[$prod['discount']])) {
                    $discountArr[$prod['discount']]['name'] = 'Скидка ' . $prod['discount'] . '%';
                    $discountArr[$prod['discount']]['id'] = $id++;
                    $discountArr[$prod['discount']]['children'] = [];
                }

                $groupArr = &$discountArr[$prod['discount']]['children'];

                if (!isset($groupArr[$prod['groupId']])) {
                    $groupArr[$prod['groupId']]['name'] = $prod['group'];
                    $groupArr[$prod['groupId']]['id'] = $id++;
                    $groupArr[$prod['groupId']]['children'] = [];
                }

                $sexArr = &$groupArr[$prod['groupId']]['children'];

                if (!isset($sexArr[$prod['sex']])) {
                    $sexArr[$prod['sex']]['name'] = $prod['sex'];
                    $sexArr[$prod['sex']]['id'] = $id++;
                    $sexArr[$prod['sex']]['children'] = [];
                }

                $modelArr = &$sexArr[$prod['sex']]['children'];

                if (!isset($modelArr[$prod['modelId']])) {
                    $modelArr[$prod['modelId']]['name'] = $prod['model'];
                    $modelArr[$prod['modelId']]['className'] = $prod['className'];
                    $modelArr[$prod['modelId']]['id'] = $id++;
                    $modelArr[$prod['modelId']]['prodArr'] = [];
                }
            }

            return $tree;
        }

        function fillBottomTree($tree, $prods)
        {
            $prods = array_filter($prods, function ($prod) {
                return $prod['divId'] === null;
            });

            $discountArr = &$tree['items'];

            foreach ($prods as $prod) {
                $groupArr = &$discountArr[$prod['discount']]['children'];
                $sexArr = &$groupArr[$prod['groupId']]['children'];
                $modelArr = &$sexArr[$prod['sex']]['children'];

                $prodArrFromModel = &$modelArr[$prod['modelId']]['prodArr'];
                $prodArrFromModel[] = $prod['cardProd'];
            }

            return $tree;
        }

        $prods = getProds();
        $normalizedProds = normalizeProds($prods, $sexTranslate);
        $topTree = makeTopTree($sexLvlTree);
        $bottomTree = makeBottomTree($normalizedProds);

        $topTree = fillTopTree($topTree, $normalizedProds);
        $bottomTree = fillBottomTree($bottomTree, $normalizedProds);

        $topTree = restructTree($topTree);
        $bottomTree = restructTree($bottomTree);

        return [
            'topTree' => $topTree,
            'bottomTree' => $bottomTree
        ];
    }

    private function getCacheForActionGetAppBarAssort()
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

        /** @var RefArtBlank[] $refArtBlanks */
        $refArtBlanks = RefArtBlank::find()
            ->where('collection_fk IS NOT NULL')
            ->andWhere(['flag_price' => 1])
            ->all();

        /** @var RefProductPrint[] $refProductPrints */
        $refProductPrints = RefProductPrint::find()
            ->where('collection_fk IS NOT NULL')
            ->andWhere(['flag_price' => 1])
            ->all();

        $prods = [];

        foreach ($refArtBlanks as $refArtBlank) {
            $prods[] = [
                'sex' => $sexTranslate[$refArtBlank->modelFk->sex_fk]['name'],
                'sexSort' => $sexTranslate[$refArtBlank->modelFk->sex_fk]['sort'],
                'category' => $refArtBlank->collectionFk->divFk->name,
                'categorySort' => $refArtBlank->collectionFk->divFk->sort,
                'class' => $refArtBlank->modelFk->classFk->oxouno
            ];
        }

        foreach ($refProductPrints as $refProductPrint) {
            $prods[] = [
                'sex' => $sexTranslate[$refProductPrint->blankFk->modelFk->sex_fk]['name'],
                'sexSort' => $sexTranslate[$refProductPrint->blankFk->modelFk->sex_fk]['sort'],
                'category' => $refProductPrint->collectionFk->divFk->name,
                'categorySort' => $refProductPrint->collectionFk->divFk->sort,
                'class' => $refProductPrint->blankFk->modelFk->classFk->oxouno
            ];
        }

        $sexSort = array_column($prods, 'sexSort');
        $categorySort = array_column($prods, 'categorySort');
        $class = array_column($prods, 'class');

        $tree = [];

        array_multisort($sexSort, $categorySort, $class, $prods);

        foreach ($prods as $prod) {
            if (!isset($tree[$prod['sexSort']])) {
                $tree[$prod['sexSort']]['name'] = $prod['sex'];
                $tree[$prod['sexSort']]['categoryArr'] = [];
            }

            $categoryArr = &$tree[$prod['sexSort']]['categoryArr'];

            if (!isset($categoryArr[$prod['categorySort']])) {
                $categoryArr[$prod['categorySort']]['name'] = $prod['category'];
                $categoryArr[$prod['categorySort']]['classArr'] = [];
            }

            $classArr = &$categoryArr[$prod['categorySort']]['classArr'];

            if (!in_array($prod['class'], $classArr)) {
                $classArr[] = $prod['class'];
            }
        }

        return $tree;
    }

    private function getCacheForActionGetAppBarDiscount()
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

        /** @var RefArtBlank[] $refArtBlanks */
        $refArtBlanks = RefArtBlank::find()
            ->where('collection_fk IS NULL')
            ->andWhere(['flag_price' => 1])
            ->all();

        /** @var RefProductPrint[] $refProductPrints */
        $refProductPrints = RefProductPrint::find()
            ->where('collection_fk IS NULL')
            ->andWhere(['flag_price' => 1])
            ->all();

        $prods = [];

        foreach ($refArtBlanks as $refArtBlank) {
            $prods[] = [
                'sex' => $sexTranslate[$refArtBlank->modelFk->sex_fk]['name'],
                'sexSort' => $sexTranslate[$refArtBlank->modelFk->sex_fk]['sort'],
                'group' => $refArtBlank->modelFk->classFk->groupFk->title,
                'groupSort' => $refArtBlank->modelFk->classFk->groupFk->sort,
                'class' => $refArtBlank->modelFk->classFk->oxouno
            ];
        }

        foreach ($refProductPrints as $refProductPrint) {
            $prods[] = [
                'sex' => $sexTranslate[$refProductPrint->blankFk->modelFk->sex_fk]['name'],
                'sexSort' => $sexTranslate[$refProductPrint->blankFk->modelFk->sex_fk]['sort'],
                'group' => $refProductPrint->blankFk->modelFk->classFk->groupFk->title,
                'groupSort' => $refProductPrint->blankFk->modelFk->classFk->groupFk->sort,
                'class' => $refProductPrint->blankFk->modelFk->classFk->oxouno
            ];
        }

        $sexSort = array_column($prods, 'sexSort');
        $groupSort = array_column($prods, 'groupSort');
        $class = array_column($prods, 'class');

        $tree = [];

        array_multisort($sexSort, $groupSort, $class, $prods);

        foreach ($prods as $prod) {
            if (!isset($tree[$prod['sexSort']])) {
                $tree[$prod['sexSort']]['name'] = $prod['sex'];
                $tree[$prod['sexSort']]['groupArr'] = [];
            }

            $groupArr = &$tree[$prod['sexSort']]['groupArr'];

            if (!isset($groupArr[$prod['groupSort']])) {
                $groupArr[$prod['groupSort']]['name'] = $prod['group'];
                $groupArr[$prod['groupSort']]['classArr'] = [];
            }

            $classArr = &$groupArr[$prod['groupSort']]['classArr'];

            if (!in_array($prod['class'], $classArr)) {
                $classArr[] = $prod['class'];
            }
        }


        return $tree;
    }
}