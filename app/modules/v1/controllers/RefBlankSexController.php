<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/2/2019
 * Time: 2:30 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefBlankClass;
use app\modules\v1\models\ref\RefBlankGroup;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefBlankSex;

class RefBlankSexController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankSex';

    const actionGetAppBarTree = 'GET /v1/ref-blank-sex/get-app-bar-tree';

    /**
     * Построить дерево для head сайта
     * @return array
     */
    public function actionGetAppBarTree()
    {
        $resp = [
            'man' => [],
            'woman' => [],
            'kids' => []
        ];


        $sexes = ['man' => [1, 5], 'woman' => [2, 5], 'kids' => [3]];

        foreach ($sexes as $sex => $sexIds) {
            $resp[$sex] = RefBlankGroup::find()
                ->joinWith(['refBlankClasses', 'refBlankClasses.refBlankModels', 'refBlankClasses.refBlankModels.sexFk'])
                ->where(['{{ref_blank_sex}}.id' => $sexIds])
                ->asArray()
                ->all();
        }


        return $resp;
    }
}