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
    static $scen;

    /**
     * @return array|false
     */
    public function fields()
    {
        //if ($this->scenario === 'azaza') {
        if (self::$scen === 'azaza') {

            $addFields =  [
                'type' => function() {
                    return 'group';
                },
                'refBlankClasses2',
            ];

        } else {

            $addFields =  [
                'type' => function() {
                    return 'group';
                },
            ];

        }


        return  array_merge(parent::fields(), $addFields);
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'refBlankClasses',
        ]);

    }

    public static function readForBaseTree()
    {
        self::$scen = 'azaza';
        return self::find()
            // ->with('refBlankClasses2')
            // ->leftJoin('refBlankClasses')
            // ->joinWith('refBlankClasses')
            // ->select(['ref_blank_group.id', 'ref_blank_group.title'])
            ->select(['id', 'title'])
            ->orderBy('title')
            ->all();
    }
}