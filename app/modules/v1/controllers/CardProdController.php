<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;

use app\extension\ProdRest;
use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\CardProd;
use app\modules\v1\classes\CardProd2;
use app\modules\v1\models\log\LogEvent;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefBlankTheme;
use app\modules\v1\models\ref\RefFabricType;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\ref\RefWeight;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsOrg;
use app\objects\Prices;
use app\objects\ProdWeight;
use Yii;
use yii\web\HttpException;

class CardProdController extends ActiveControllerExtended
{
    public $modelClass = '';


    const actionGetByFilters = 'GET /v1/card-prod/get-by-filters';

    /**
     * Вернуть все изделия соответствующие фильтрам
     * @param $form - {search: "", "sexTags":["Мужчинам"],"groupIDs":[],"classTags":["Футболка"],"themeTags":[],"fabTypeTags":[],"newOnly":false,"print":"all"}
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetByFilters($form)
    {
        return CardProd::getByFilters($form);
    }

    const actionGetByFilters2 = 'GET /v1/card-prod/get-by-filters2';

    /**
     * Вернуть все изделия соответствующие фильтрам v2
     * @param $form -
     * @return array
     * @throws \Exception
     */
    public function actionGetByFilters2($form)
    {
        return CardProd::getByFilters2($form);
    }

    const actionGetDetails = 'GET /v1/card-prod/get-details';

    /**
     * Вернуть размеры и остатки по складу
     * @param $legalEntityID
     * @param $prodId
     * @param int $printId
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetDetails($prodId, $printId = 1, $legalEntityID = 0)
    {
        $prodId = (int)$prodId;
        $printId = (int)$printId;

        // Скидка клиента

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();
        $legalEntity = SlsClient::findOne(['id' => $legalEntityID]);

        $clientDiscount = 0;

        if ($legalEntity && $legalEntity->discount) {
            if ($contact->org_fk !== $legalEntity->org_fk) {
                throw new HttpException(200, "Юр. лицо с этим ID не состоит в вашей организации.", 200);
            }

            $clientDiscount = $legalEntity->discount;
        } else {
            $org = SlsOrg::findOne(['id' => $contact->org_fk]);
            $clientDiscount = $org->discount;
        }

        // Получение информации

        /** @var $prod RefArtBlank */
        $prod = RefArtBlank::get($prodId);

        $postProd = null;

        if ($printId !== 1) {
            /** @var RefProductPrint $postProd */
            $postProd = RefProductPrint::find()
                ->where(['blank_fk' => $prodId])
                ->andWhere(['print_fk' => $printId])
                ->one();
        }

        $sexType = $prod->calcSizeType();

        $prodRest = new ProdRest([$prodId]);
        $weight = RefWeight::readRec($prod->model_fk, $prod->fabric_type_fk);

        $resp = [
            "infoArr" => [],
            "discountBiggerThan29Flag" => false
        ];

        $prices = new Prices();

        // Скидка товара

        $prodDiscount = $prices->getDiscount($prodId, $printId);

        // Полная скидка

        $totalDiscount = (1 - $clientDiscount / 100) * (1 - $prodDiscount / 100);

        if ($totalDiscount < 0.71) {
            $resp['discountBiggerThan29Flag'] = true;
            $totalDiscount = 0.71;
        }

        foreach (Sizes::prices as $fSize => $fPrice) {
            $price = $prices->getPrice($prodId, $printId, $fSize);

            if ($price) {

                $rest = $prodRest->getAvailForOrder($prodId, $printId, 1, $fSize);
                if ($rest <= 0) {
                    $restColor = '#d4000018';
                    $restStr = 0;
                } elseif ($rest > 0 && $rest <= 10) {
                    $restColor = '#d4d40018';
                    $restStr = $rest;
                } else {
                    $restColor = '#00d40018';
                    $restStr = '> 10';
                }

                $resp['infoArr'][] = [
                    // 'fSize' => $fSize,
                    'sizeStr' => Sizes::typeCompare[$sexType][$fSize],
                    'size' => $fSize,
                    'price' => $price,
                    'priceDiscount' => round($price * $totalDiscount),
                    'doublePrice' => $price * 2,
                    'restStr' => $restStr,
                    'restColor' => $restColor,
                    'weight' => $weight->$fSize,
                ];
            }
        }

        return $resp;
    }

    const actionGetCard = 'GET /v1/card-prod/get-card';

    /**
     * Вернуть карточку товара (ta-v2)
     * @param $prodId
     * @param $printId
     * @param $packId
     * @return array
     * @throws \Exception
     */
    public function actionGetCard(int $prodId, int $printId, int $packId)
    {
        if ($printId === 1) {
            $prod = RefArtBlank::readProd($prodId);
            $blank = $prod;
        } else {
            $prod = RefProductPrint::readProd($prodId, $printId);
            $blank = $prod->blankFk;
        }

        if ($prod === null) {
            throw new HttpException(200, "Такого товара не существует {$prodId}-$printId", 200);
        }

        $card = new CardProd2($prod, $packId);
        $rests = new ProdRest();
        $weights = new ProdWeight();

        $tableSizes = [];
        foreach (Sizes::prices as $fSize => $fPrice) {
            if ($prod->$fPrice > 0) {

                $tableSizes[] = [
                    'size' => $blank->calcSizeStr($fSize),
                    'weight' => $weights->getWeight($blank->model_fk, $blank->fabric_type_fk, $fSize),
                    'basePrice' => $prod->$fPrice,
                    'rest' => $rests->getRest($prodId, $printId, $packId, $fSize),

                ];
            }
        }


        return [
            'class' => $card->class,
            'art' => $card->art,
            'model' => $card->modelFk->fashion,
            'modelDescript' => $card->modelFk->descript,
            'color' => $card->themeFk->title,
            'print' => $card->printFk->title,

            'fabricType' => $card->fabricTypeFk->type,
            'fabricStruct' => $card->fabricTypeFk->struct,
            'fabricDensity' => $card->fabricTypeFk->desity . ' г/м2',
            'pack' => $card->packFk->title,
            'packId' => $card->packFk->id,
            'tableSizes' => $tableSizes,

            'photos' => $card->photos,
        ];

    }


    const actionGetAppBarAssort = 'GET /v1/card-prod/get-app-bar-assort';

    public function actionGetAppBarAssort()
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

    const actionGetAppBarDiscount = 'GET /v1/card-prod/get-app-bar-discount';

    public function actionGetAppBarDiscount()
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
