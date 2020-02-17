<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankClass;
use app\gii\GiiRefProdPrint;
use app\gii\GiiRefProductPrint;

/**
 * Class RefBlankClass
 * @property RefBlankModel[] $refBlankModelsTree
 */
class RefProdPrint extends GiiRefProdPrint
{
    public function hArt()
    {
        if ($this->id == 1) {
            return '000';
        } else {
            return str_pad($this->id, 3, '0', STR_PAD_LEFT);
        }
    }
}