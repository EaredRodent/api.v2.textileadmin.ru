<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/17/2019
 * Time: 12:15 PM
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsMessageState;

class SlsMessageState extends GiiSlsMessageState
{
    public function fields()
    {
        return array_merge(parent::fields(), []);
    }
}