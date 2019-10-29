<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\CardProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefProductPrint;

class CardProdController extends ActiveControllerExtended
{
    public $modelClass = '';


    const actionGetByFilters = 'GET /v1/card-prod/get-by-filters';

    /**
     * Вернуть все изделия соответствующие фильтрам
     * @param $form - {"sexTags":["Мужчинам"],"groupIDs":[],"classTags":["Футболка"],"themeTags":[],"fabTypeTags":[],"newOnly":false,"print":"all"}
     * @return array
     */
    public function actionGetByFilters($form)
    {
        $form = json_decode($form, true);

        $sexTags = isset($form['sexTags']) ? $form['sexTags'] : [];
        $sexTitles = RefBlankSex::calcSexTagsToRealTitles($sexTags);

        $groupIDs = isset($form['groupIDs']) ? $form['groupIDs'] : [];
        $classTags = isset($form['classTags']) ? $form['classTags'] : [];
        $themeTags = isset($form['themeTags']) ? $form['themeTags'] : [];
        $fabTypeTags = isset($form['fabTypeTags']) ? $form['fabTypeTags'] : [];
        $newOnly = isset($form['newOnly']) ? $form['newOnly'] : false;

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

        $filteredProds = RefArtBlank::readFilterProds(
            $newProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags);

        $arrCards = [];
        foreach ($filteredProds as $prod) {
            $arrCards[] = new CardProd($prod);
        }

        $filteredProdsPrint = RefProductPrint::readFilterProds(
            $newPrintProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        foreach ($filteredProdsPrint as $prodPrint) {
            $arrCards[] = new CardProd($prodPrint);
        }

        usort($arrCards, function ($a, $b) {
            if($a->art < $b->art) {
                return -1;
            }
            if($a->art > $b->art) {
                return 1;
            }
            return 0;
        });

//        $prods = [];
//        foreach (array_merge($filteredProds, $filteredProdsPrint) as $prod) {
//            $prods[] = new CardProd($prod);
//        }

        return $arrCards;

    }
}