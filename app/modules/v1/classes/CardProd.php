<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 30.05.2019
 * Time: 12:50
 */

namespace app\modules\v1\classes;

use app\extension\ProdRest;
use app\extension\Sizes;
use app\modules\v1\models\log\LogEvent;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankSex;
use app\modules\v1\models\ref\RefBlankTheme;
use app\modules\v1\models\ref\RefCollection;
use app\modules\v1\models\ref\RefEan;
use app\modules\v1\models\ref\RefFabricType;
use app\modules\v1\models\ref\RefProdPack;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\ref\RefProductPrint;
use yii\web\HttpException;


class CardProd
{
    // ? public $printId; // OXO-NNNN-PPP

    public $prodId; // OXO-NNNN
    public $printId;

    public $titleStr;
    public $art;
    public $class;
    public $photos;
    public $minPrice;
    public $sizes;

    public $fabricTypeFk;
    public $modelFk;
    public $themeFk;
    public $printFk;
    public $packFk;
    public $flagRest; // 1 - если есть остатки на складе по этому изделию
    public $discount;
    public $discountPrice;
    public $categoryStr;
    public $collectionStr;
    public $groupStr;
    public $hClientArt;

    public $price_5xs;
    public $price_4xs;
    public $price_3xs;
    public $price_2xs;
    public $price_xs;
    public $price_s;
    public $price_m;
    public $price_l;
    public $price_xl;
    public $price_2xl;
    public $price_3xl;
    public $price_4xl;

    public $age;

    /**
     * CardProd constructor.
     * @param RefArtBlank|RefProductPrint $objProd
     * @param ProdRest $prodRest
     * @throws \Exception
     */
    function __construct($objProd, &$prodRest = null)
    {
        $this->prodId = $objProd->calcProdId();
        $this->titleStr = $objProd->fields()['titleStr']();
        $this->art = $objProd->fields()['art']();
        $this->class = $objProd->fields()['class']();
        $this->photos = $objProd->fields()['photos']();
        $this->minPrice = $objProd->fields()['minPrice']();
        $this->sizes = $objProd->fields()['sizes']();

        foreach (Sizes::prices as $fSize => $fPrice) {
            $this->$fPrice = $objProd->$fPrice;
        }


        $prod = isset($objProd->blank_fk) ? $objProd->blankFk : $objProd;

        $this->fabricTypeFk = $prod->fabricTypeFk;
        $this->modelFk = $prod->modelFk;
        $this->themeFk = $prod->themeFk;

        $this->categoryStr = $objProd->collection_fk ? $objProd->collectionFk->divFk->name : '';
        $this->collectionStr = $objProd->collection_fk ? $objProd->collectionFk->name : '';
        $this->groupStr = $prod->modelFk->classFk->groupFk->title;

        $this->printId = isset($objProd->print_fk) ? $objProd->print_fk : 1;
        $this->printFk = isset($objProd->print_fk) ? $objProd->printFk : RefProdPrint::findOne(['id' => 1]);

        // Всегда полиэтилен todo !!!
        $this->packFk = RefProdPack::findOne(1);

        // Установка flagRest (если есть хоть что-то -- то true)
        $this->flagRest = 0;
        if ($prodRest) {
            foreach (Sizes::fields as $fSize) {
                $rest = $prodRest->getAvailForOrder($this->prodId, $this->printFk->id, 1, $fSize);
                if ($rest > 0) {
                    $this->flagRest = 1;
                    break;
                }
            }
        }

        $this->discount = $objProd->fields()['discount']();
        $this->discountPrice = round($this->minPrice * (1 - $this->discount / 100));

        $this->hClientArt = $prod->hClientArt($this->printId);

        // age

        $tsCreate = isset($objProd->print_fk) ? $objProd->ts_create : $objProd->dt_create;
        $this->age = (int)((time() - strtotime($tsCreate)) / (3600 * 24));
    }


    static function sort(&$arrCards)
    {
        usort($arrCards, function ($a, $b) {
            if ($a->art < $b->art) {
                return 1;
            }
            if ($a->art > $b->art) {
                return -1;
            }
            return 0;
        });
    }

    /**
     * todo что это?
     * @param $arrCards
     * @param $search
     */
    static function search(&$arrCards, $search)
    {
        if (!$search) {
            return;
        }

        $search = mb_strtolower($search);

        $arrCards = array_filter($arrCards, function ($el) use ($search) {
            $jsonCard = mb_strtolower(json_encode($el, JSON_UNESCAPED_UNICODE));
            return strpos($jsonCard, $search) !== false;
        });
    }

    /**
     * Вернуть все изделия соответствующие фильтрам
     * @param $form - {search: "", "sexTags":["Мужчинам"],"groupIDs":[],"classTags":["Футболка"],"themeTags":[],"fabTypeTags":[],"newOnly":false,"print":"all"}
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    static public function getByFilters($form)
    {
        $form = json_decode($form, true);

        $sexTags = isset($form['sexTags']) ? $form['sexTags'] : [];
        $sexTitles = RefBlankSex::calcSexTagsToRealTitles($sexTags);

        $search = isset($form['search']) ? $form['search'] : '';
        $newOnly = isset($form['newOnly']) ? $form['newOnly'] : false;
        $discountOnly = isset($form['discountOnly']) ? $form['discountOnly'] : false;
        $collectionIDs = isset($form['collectionIDs']) ? $form['collectionIDs'] : [];
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
            $newProdIDs = RefArtBlank::calcNewProdIDs();
            $newPrintProdIDs = RefProductPrint::calcNewProdIDs();
        }
        if ($newOnly && $print === 'yes') {
            $newPrintProdIDs = RefProductPrint::calcNewProdIDs();
        }
        if ($newOnly && $print === 'no') {
            $newProdIDs = RefArtBlank::calcNewProdIDs();
        }

        $filteredProds = [];
        $filteredProdsPrint = [];

        if ($print === 'all') {
            $filteredProds = RefArtBlank::readFilterProds($newProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, $themeTags, $fabTypeTags);
            $filteredProdsPrint = RefProductPrint::readFilterProds($newPrintProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        }
        if ($print === 'yes') {
            $filteredProdsPrint = RefProductPrint::readFilterProds($newPrintProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        }
        if ($print === 'no') {
            $filteredProds = RefArtBlank::readFilterProds($newProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, $themeTags, $fabTypeTags);
        }

        $prodRests = new ProdRest();

        /** @var $arrCards CardProd[] */
        $arrCards = [];
        foreach (array_merge($filteredProds, $filteredProdsPrint) as $prod) {
            $arrCards[] = new CardProd($prod, $prodRests);
        }

        CardProd::search($arrCards, $search);

        CardProd::sort($arrCards);

        // Вычисление доступных цветов и тканей для текущих фильтров
        // * Фильтрует игнорируя установленные пользоватеелем фильтры цвет/ткань, иначе доступными будут только они

        $filteredProds2 = [];
        $filteredProdsPrint2 = [];

        if ($print === 'all') {
            $filteredProds2 = RefArtBlank::readFilterProds($newProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, [], []);
            $filteredProdsPrint2 = RefProductPrint::readFilterProds($newPrintProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, [], []);
        }
        if ($print === 'yes') {
            $filteredProdsPrint2 = RefProductPrint::readFilterProds($newPrintProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, [], []);
        }
        if ($print === 'no') {
            $filteredProds2 = RefArtBlank::readFilterProds($newProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, [], []);
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

        LogEvent::log(LogEvent::filterCatalog);

        return [
            'filteredProds' => $arrCards,
            'availableRefBlankTheme' => $availableRefBlankTheme,
            'availableRefFabricType' => $availableRefFabricType
        ];
    }

    /**
     * Вернуть все изделия соответствующие фильтрам
     * @param $form -
     * @return array
     * @throws \Exception
     */
    static public function getByFilters2($form)
    {
        $form = json_decode($form, true);
        $categoryID = isset($form['categoryID']) ? $form['categoryID'] : null;
        $collectionID = isset($form['collectionID']) ? $form['collectionID'] : null;
        $sexName = isset($form['sexName']) ? $form['sexName'] : null;
        $sexTitles = RefBlankSex::calcSexTagsToRealTitles([$sexName]);
        $modelID = isset($form['modelID']) ? $form['modelID'] : null;
        $discountNumber = isset($form['discountNumber']) ? $form['discountNumber'] : null;
        $groupID = isset($form['groupID']) ? $form['groupID'] : null;


        $filteredProds = RefArtBlank::readFilterProds2($categoryID, $collectionID, $sexTitles, $modelID, $discountNumber, $groupID);
        $filteredProdsPrint = RefProductPrint::readFilterProds2($categoryID, $collectionID, $sexTitles, $modelID, $discountNumber, $groupID);

        $prodRests = new ProdRest();

        /** @var $arrCards CardProd[] */
        $arrCards = [];
        foreach (array_merge($filteredProds, $filteredProdsPrint) as $prod) {
            $arrCards[] = new CardProd($prod, $prodRests);
        }

        CardProd::sort($arrCards);

        LogEvent::log(LogEvent::filterCatalog);


        /** @var RefCollection $refCollection */
        $refCollection = null;

        if ($collectionID && !$sexName && !$modelID) {
            /** @var RefCollection $refCollection */
            $refCollection = RefCollection::findOne(['id' => $collectionID]);
        }

        return [
            'prods' => $arrCards,
            'collection' => $refCollection
        ];
    }

}
