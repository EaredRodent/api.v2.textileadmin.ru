<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsClient;

class SlsClient extends GiiSlsClient
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'managerFk'
        ]);
    }

    /**
     * @return array|self[]
     */
    public static function readAllSort()
    {
        return self::find()
            ->with('slsOrders')
            ->orderBy('short_name')
            ->all();
    }
}