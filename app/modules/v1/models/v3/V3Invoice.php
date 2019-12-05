<?php

namespace app\modules\v1\models\v3;

use app\gii\GiiV3Invoice;

/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/28/2019
 * Time: 2:39 PM
 */

class V3Invoice extends GiiV3Invoice
{
    public $sum_pay;
    public $ts_pay;

    public $countEvent;

    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk',
            'typeFk',
            'countEvent',
            'sum_pay',
            'ts_pay'
        ]);
    }
}