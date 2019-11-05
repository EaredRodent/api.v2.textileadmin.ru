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
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\CardProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefBlankTheme;
use app\modules\v1\models\ref\RefFabricType;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\ref\RefWeight;

class CardProdController extends ActiveControllerExtended
{
    public $modelClass = '';


    const actionGetByFilters = 'GET /v1/card-prod/get-by-filters';

    /**
     * Вернуть все изделия соответствующие фильтрам
     * @param $form - {search: "", "sexTags":["Мужчинам"],"groupIDs":[],"classTags":["Футболка"],"themeTags":[],"fabTypeTags":[],"newOnly":false,"print":"all"}
     * @return array
     */
    public function actionGetByFilters($form)
    {
        $form = json_decode($form, true);

        $sexTags = isset($form['sexTags']) ? $form['sexTags'] : [];
        $sexTitles = RefBlankSex::calcSexTagsToRealTitles($sexTags);

        $search = isset($form['search']) ? $form['search'] : '';
        $newOnly = isset($form['newOnly']) ? $form['newOnly'] : false;
        $groupIDs = isset($form['groupIDs']) ? $form['groupIDs'] : [];
        $classTags = isset($form['classTags']) ? $form['classTags'] : [];
        $themeTags = isset($form['themeTags']) ? $form['themeTags'] : [];
        $fabTypeTags = isset($form['fabTypeTags']) ? $form['fabTypeTags'] : [];

        // yes - только принт
        // no - без принта
        // all - все равно
        $print = isset($form['print']) ? $form['print'] : 'all';

        $newProdIDs = [];
        $newPrintProdIDs = [];

        if ($newOnly && $print === 'all') {
            $newProdIDs = RefArtBlank::calcNewProdIDs(30);
            $newPrintProdIDs = RefProductPrint::calcNewProdIDs(30);
        }
        if ($newOnly && $print === 'yes') {
            $newPrintProdIDs = RefProductPrint::calcNewProdIDs(30);
        }
        if ($newOnly && $print === 'no') {
            $newProdIDs = RefArtBlank::calcNewProdIDs(30);
        }

        $filteredProds = [];
        $filteredProdsPrint = [];

        if ($print === 'all') {
            $filteredProds = RefArtBlank::readFilterProds($newProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags);
            $filteredProdsPrint = RefProductPrint::readFilterProds($newPrintProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        }
        if ($print === 'yes') {
            $filteredProdsPrint = RefProductPrint::readFilterProds($newPrintProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        }
        if ($print === 'no') {
            $filteredProds = RefArtBlank::readFilterProds($newProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        }

        $arrCards = [];
        foreach (array_merge($filteredProds, $filteredProdsPrint) as $prod) {
            $arrCards[] = new CardProd($prod);
        }

        CardProd::search($arrCards, $search);

        CardProd::sort($arrCards);

        // Вычисление доступных цветов и тканей для текущих фильтров
        // * Фильтрует игнорируя установленные пользоватеелем фильтры цвет/ткань, иначе доступными будут только они

        $filteredProds2 = [];
        $filteredProdsPrint2 = [];

        if ($print === 'all') {
            $filteredProds2 = RefArtBlank::readFilterProds($newProdIDs, $sexTitles, $groupIDs, $classTags, [], []);
            $filteredProdsPrint2 = RefProductPrint::readFilterProds($newPrintProdIDs, $sexTitles, $groupIDs, $classTags, [], []);
        }
        if ($print === 'yes') {
            $filteredProdsPrint2 = RefProductPrint::readFilterProds($newPrintProdIDs, $sexTitles, $groupIDs, $classTags, [], []);
        }
        if ($print === 'no') {
            $filteredProds2 = RefArtBlank::readFilterProds($newProdIDs, $sexTitles, $groupIDs, $classTags, [], []);
        }

        /** @var CardProd[] $arrCards2 */
        $arrCards2 = [];
        foreach (array_merge($filteredProds2, $filteredProdsPrint2) as $prod) {
            $arrCards2[] = new CardProd($prod);
        }

        $availableRefBlankTheme = [];
        $availableRefFabricType = [];

        foreach ($arrCards2 as $card) {
            if ($card->themeFk && !in_array($card->themeFk->id, $availableRefBlankTheme)) {
                $availableRefBlankTheme[] = $card->themeFk->id;
            }
            if ($card->fabricTypeFk && !in_array($card->fabricTypeFk->id, $availableRefFabricType)) {
                $availableRefFabricType[] = $card->fabricTypeFk->id;
            }
        }

        $availableRefBlankTheme = RefBlankTheme::find()
            ->where(['id' => $availableRefBlankTheme])
            ->groupBy('title_price')
            ->all();

        $availableRefFabricType = RefFabricType::find()
            ->where(['id' => $availableRefFabricType])
            ->groupBy('type_price')
            ->all();

        return [
            'filteredProds' => $arrCards,
            'availableRefBlankTheme' => $availableRefBlankTheme,
            'availableRefFabricType' => $availableRefFabricType
        ];

    }

    const actionGetDetails = 'GET /v1/card-prod/get-details';

    /**
     * Вернуть размеры и остатки по складу
     * @param $prodId
     * @param int $printId
     * @return array
     */
    public function actionGetDetails($prodId, $printId = 1)
    {
        $prodId = (int) $prodId;
        $printId = (int) $printId;

        /** @var $prod RefArtBlank */
        $prod = RefArtBlank::get($prodId);

        $postProd = null;

        if($printId !== 1) {
            /** @var RefProductPrint $postProd */
            $postProd = RefProductPrint::find()
                ->where(['blank_fk' => $prodId])
                ->andWhere(['print_fk' => $printId])
                ->one();
        }

        $sexType = $prod->calcSizeType();

        $prodRest = new ProdRest([$prodId]);
        $weight = RefWeight::readRec($prod->model_fk, $prod->fabric_type_fk);

        $resp = [];

        $priceModel = $printId === 1 ? $prod : $postProd;

        foreach (Sizes::prices as $fSize => $fPrice) {
            if ($priceModel->$fPrice > 0) {

                $rest = $prodRest->getRestPrint($prodId, $printId, $fSize);
                if ($rest == 0) {
                    $restColor = '#d4000018';
                    $restStr = $rest;
                } elseif ($rest > 0 && $rest <= 10) {
                    $restColor = '#d4d40018';
                    $restStr = $rest;
                } else {
                    $restColor = '#00d40018';
                    $restStr = '> 10';
                }

                $resp[] = [
                    // 'fSize' => $fSize,
                    'sizeStr' => Sizes::typeCompare[$sexType][$fSize],
                    'size' => $fSize,
                    'price' => $priceModel->$fPrice,
                    'restStr' => $restStr,
                    'restColor' => $restColor,
                    'weight' => $weight->$fSize,
                ];
            }
        }

        return $resp;
    }
}