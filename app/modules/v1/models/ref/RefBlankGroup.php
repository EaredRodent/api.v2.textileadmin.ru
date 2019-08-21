<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankGroup;

class RefBlankGroup extends GiiRefBlankGroup
{
    /**
     * @return array|false
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
            //'refBlankClasses',
        ]);
    }
}