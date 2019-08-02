<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:25
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsOrder;
use app\gii\GiiSlsPreorder;
use yii\db\ActiveRecord;

class SlsPreorder extends GiiSlsPreorder
{

    public function fields()
    {
        return array_merge(parent::fields(), [
            'clientFk'
        ]);
    }

}