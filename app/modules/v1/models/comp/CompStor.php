<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\comp;

use app\gii\GiiCompStor;


class CompStor extends GiiCompStor
{

    /**
     * @return array|self
     */
    static function readRests()
    {
        return self::find()
            ->select('{{comp_stor}}.*, sum(count) AS count')
            ->groupBy('item_fk')
            ->all();
    }

    static function calcAvgPrices()
    {
        $resp = [];

        /** @var $arr CompStor[] */
        $arr = self::find()
            ->select('{{comp_stor}}.*, AVG(price) AS price')
            ->groupBy('item_fk')
            ->all();

        foreach ($arr as $rec) {
            $resp[$rec->item_fk] = round($rec->price, 2);
        }

        return $resp;
    }

}