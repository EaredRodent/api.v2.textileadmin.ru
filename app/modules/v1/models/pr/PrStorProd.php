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


    // ENUM('in-balance','in-manual','in-production','in-return','in-invent','out-sale','out-print','out-pack','out-order','out-invent','out-prod')
    public $totalSum;

    /**
     * Вернуть остатки по склад для заданных артикулов. Если артикулы не заданы - вернуть весь склад
     * @param $prodIds
     * @return self[]
     */
    public static function readRest($prodIds)
    {
        return self::find()
            ->select(array_merge(['{{pr_stor_prod}}.*'], Sizes::selectSum2))
            ->where('exec = 1')
            ->andFilterWhere(['blank_fk' => $prodIds])
            ->groupBy('blank_fk, print_fk, pack_fk')
            ->all();
    }

    /**
     * @param $typeDirect
     * @param $start
     * @param $end
     * @return array|self[]
     */
    public static function readRecs($typeDirect, $start, $end)
    {
        return self::find()
            ->where(['type' => $typeDirect])
            ->andWhere('dt_move >= :dateStart', [':dateStart' => $start])
            ->andWhere('dt_move <= :dateEnd', [':dateEnd' => $end])
            ->orderBy('dt_move')
            ->all();
    }
}