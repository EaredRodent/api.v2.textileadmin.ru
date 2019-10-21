<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 3:15 PM
 */

namespace app\modules\v1\models\sls;


use app\extension\Sizes;
use app\gii\GiiSlsItem;

class SlsItem extends GiiSlsItem
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'blankFk'
        ]);
    }
}