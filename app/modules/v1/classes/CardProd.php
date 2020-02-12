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
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefEan;
use app\modules\v1\models\ref\RefProdPack;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\ref\RefProductPrint;


class CardProd
{
    // ? public $printId; // OXO-NNNN-PPP

    public $prodId; // OXO-NNNN

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

    /**
     * CardProd constructor.
     * @param RefArtBlank|RefProductPrint $objProd
     * @param ProdRest $prodRest
     * @throws \Exception
     */
    function __construct($objProd, $prodRest = null)
    {
        $this->prodId = $objProd->calcProdId();
        $this->titleStr = $objProd->fields()['titleStr']();
        $this->art = $objProd->fields()['art']();
        $this->class = $objProd->fields()['class']();
        $this->photos = $objProd->fields()['photos']();
        $this->minPrice = $objProd->fields()['minPrice']();
        $this->sizes = $objProd->fields()['sizes']();


        $prod = isset($objProd->blank_fk) ? $objProd->blankFk : $objProd;

        $this->fabricTypeFk = $prod->fabricTypeFk;
        $this->modelFk = $prod->modelFk;
        $this->themeFk = $prod->themeFk;

        $this->printFk = isset($objProd->print_fk) ? $objProd->printFk : RefProdPrint::findOne(['id' => 1]);

        // Всегда полиэтилен todo !!!
        $this->packFk = RefProdPack::findOne(1);

        // Установка flagRest (если есть хоть что-то -- то true)
        $this->flagRest = 0;
        if ($prodRest) {
            foreach (Sizes::fields as $fSize) {
                $rest = $prodRest->getAvailForOrder($this->prodId, $this->printFk->id, 1, $fSize);
                if($rest > 0) {
                    $this->flagRest = 1;
                    break;
                }
            }
        }

        $this->discount = $objProd->fields()['discount']();
        $this->discountPrice = $this->minPrice * (1 - $this->discount / 100);
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
}