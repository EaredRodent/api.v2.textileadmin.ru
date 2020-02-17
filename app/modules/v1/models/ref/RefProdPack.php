<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 5:13 PM
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefProdPack;

class RefProdPack extends GiiRefProdPack
{
    public function hArt()
    {
        if ($this->id == 1) {
            return '00';
        } else {
            return str_pad($this->id, 2, '0', STR_PAD_LEFT);
        }
    }
}