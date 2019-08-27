<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankClass;

/**
 * Class RefBlankClass
 * @property RefBlankModel[] $refBlankModelsTree
 */
class RefBlankClass extends GiiRefBlankClass
{

    static $setFiels = [];

    /**
     * @return array|false
     */
    public function fields()
    {
        $fields = [
            'type' => function () {
                return 'class';
            },
            'children' => 'refBlankModelsTree',

        ];

        $addFields = [];
        foreach (self::$setFiels as $fItem) {
            $addFields[$fItem] = $fields[$fItem];
        }

        return array_merge(parent::fields(), $addFields);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefBlankModelsTree()
    {
        return $this->hasMany(RefBlankModel::className(), ['class_fk' => 'id'])
            ->joinWith('sexFk')
            ->orderBy('ref_blank_sex.code_ru, title');
    }
}