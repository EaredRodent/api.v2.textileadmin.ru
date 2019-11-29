<?php

namespace app\modules\v1\models\v3;

use app\gii\GiiV3MoneyEvent;

/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/28/2019
 * Time: 2:39 PM
 */

class V3MoneyEvent extends GiiV3MoneyEvent
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'invoiceFk'
        ]);
    }
}