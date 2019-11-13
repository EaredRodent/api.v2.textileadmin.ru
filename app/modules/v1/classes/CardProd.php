<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 30.05.2019
 * Time: 12:50
 */

namespace app\modules\v1\classes;

use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefEan;
use app\modules\v1\models\ref\RefProdPack;
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

    /**
     * CardProd constructor.
     * @param RefArtBlank|RefProductPrint $objProd
     */
    function __construct($objProd)
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

        $this->printFk = isset($objProd->print_fk) ? $objProd->printFk : null;

        /** @var  RefEan $ean */
//        $ean = RefEan::find()
//            ->where(['blank_fk' => $prod->id])
//            ->andWhere(['print_fk' => $this->printFk ? $this->printFk->id : 1])
//            ->one();

        // Всегда полиэтилен
        $this->packFk = RefProdPack::findOne(1);

    }


    static function sort(&$arrCards)
    {
        usort($arrCards, function ($a, $b) {
            if ($a->art < $b->art) {
                return -1;
            }
            if ($a->art > $b->art) {
                return 1;
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

    public function getPrintId()
    {
        if (isset($this->printFk)) {
            return $this->printFk->id;
        } else {
            return 1;
        }
    }
}