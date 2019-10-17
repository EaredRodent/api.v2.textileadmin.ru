<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/17/2019
 * Time: 12:15 PM
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsMessage;

class SlsMessage extends GiiSlsMessage
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk'
        ]);
    }
}