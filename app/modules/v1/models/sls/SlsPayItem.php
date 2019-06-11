<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10.06.2019
 * Time: 13:09
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsPayItem;
use yii\db\ActiveRecord;

class SlsPayItem extends GiiSlsPayItem
{
    /**
     * @return array|ActiveRecord[]|self[]
     */
    public static function getOut()
    {
        return self::find()
            ->where(['direct' => 'out'])
            ->orderBy('name')
            ->all();
    }

    /**
     * @return array|ActiveRecord[]|self[]
     */
    public static function getIn()
    {
        return self::find()
            ->where(['direct' => 'in'])
            ->orderBy('name')
            ->all();
    }
}