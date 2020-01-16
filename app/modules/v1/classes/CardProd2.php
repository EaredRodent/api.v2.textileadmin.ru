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


class CardProd2
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

    /**
     * CardProd constructor.
     * @param RefArtBlank|RefProductPrint $objProd
     * @param $packId
     */
    function __construct($objProd, $packId)
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

        $this->printFk = isset($objProd->print_fk) ?
            $objProd->printFk : RefProdPrint::findOne(['id' => 1]);

        $this->packFk = RefProdPack::findOne($packId);

    }


}