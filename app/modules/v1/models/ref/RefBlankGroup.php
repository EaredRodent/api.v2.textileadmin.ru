<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankGroup;


/**
 * Class RefBlankGroup
 * @package app\modules\v1\models\ref
 *
 * @property RefBlankClass[] $refBlankClassesTree
 */
class RefBlankGroup extends GiiRefBlankGroup
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefBlankClassesTree()
    {
        //RefBlankClass::$setFiels = ['type', 'children'];
        return $this->hasMany(RefBlankClass::className(), ['group_fk' => 'id'])
            ->orderBy('title');
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function readAllSort()
    {
        return self::find()
            ->orderBy('title')
            ->all();
    }
}