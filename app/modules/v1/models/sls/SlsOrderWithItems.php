<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/21/2019
 * Time: 6:06 PM
 */

namespace app\modules\v1\models\sls;


class SlsOrderWithItems extends SlsOrder
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'items' => function () {
                return SlsItemAdvanced::find()
                    ->where(['order_fk' => $this->id])
                    ->all();
            }
        ]);
    }
}