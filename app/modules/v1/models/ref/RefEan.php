<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 5:13 PM
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefEan;

class RefEan extends GiiRefEan
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'packFk'
        ]);
    }
}