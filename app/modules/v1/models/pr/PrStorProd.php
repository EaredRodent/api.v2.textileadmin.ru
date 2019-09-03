<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\pr;

use app\extension\Sizes;
use app\gii\GiiPrStorProd;

class PrStorProd extends GiiPrStorProd
{

    public $totalSum;

    public static function readRest($prodIds)
    {
        return self::find()
            ->select(array_merge(['{{pr_stor_prod}}.*'], Sizes::selectSum2))
            ->where('exec = 1')
            ->andFilterWhere(['blank_fk' => $prodIds])
            ->groupBy('blank_fk, print_fk, pack_fk')
            ->all();
    }
}