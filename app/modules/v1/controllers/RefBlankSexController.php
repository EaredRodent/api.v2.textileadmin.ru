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
use yii\db\Query;

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
            $resp[$sex] = (new Query())
                ->select('ref_blank_class.tag AS classTitle, ref_blank_class.id AS classId, ref_blank_group.title AS groupTitle, ref_blank_group.id AS groupId')
                ->distinct()
                ->from('ref_blank_model')
                ->leftJoin('ref_blank_class', 'ref_blank_model.class_fk = ref_blank_class.id')
                ->leftJoin('ref_blank_group', 'ref_blank_class.group_fk = ref_blank_group.id')
                ->leftJoin('ref_blank_sex', 'ref_blank_model.sex_fk = ref_blank_sex.id')
                ->where(['ref_blank_sex.id' => $sexIds])
                ->all();
        }


        return $resp;
    }
}