<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\pr;

use app\gii\GiiPrStorFabric;


class PrStorFabric extends GiiPrStorFabric
{

    // ENUM('s1_storage','s2_cutws','s3_deck','s4_worked','s5_defect')
    // Статусы рулонов
    const s1_storage = 's1_storage';
    const s2_cutws   = 's2_cutws';
    const s3_deck    = 's3_deck';
    const s4_worked  = 's4_worked';
    const s5_defect  = 's5_defect';

    public $countRoll;

    const selectSumParams = [
        '{{pr_stor_fabric}}.*',
        'sum(w_brutto) AS w_brutto',
        'sum(w_netto) AS w_netto',
        'sum(w_current) AS w_current',
        'sum(price * w_current / 1000) AS price',
        'count(pr_stor_fabric.id) AS countRoll',
    ];

    /**
     * Вернуть остатки на складе ткани - итого
     * @return array|PrStorFabric
     */
    public static function readRestTotal()
    {
        return self::find()
            ->select(self::selectSumParams)
            ->where(['state' => self::s1_storage])
            ->groupBy('state')
            ->one();
    }
}