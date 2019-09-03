<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankClass;
use app\gii\GiiRefBlankSex;
use app\gii\GiiRefWeight;

class RefWeight extends GiiRefWeight
{

    /**
     * @param $modelId
     * @param $fabricId
     * @return array|self
     */
    public static function readRec($modelId, $fabricId)
    {
         $resp = self::find()
            ->where(['model_fk' => $modelId, 'fabric_fk' => $fabricId])
            ->one();
         return ($resp) ? $resp : new RefWeight();
    }
}