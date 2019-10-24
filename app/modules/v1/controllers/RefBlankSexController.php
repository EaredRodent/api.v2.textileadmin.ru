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

    const actionGetSexTags = 'GET /v1/ref-blank-sex/get-sex-tags';

    /**
     * Вернуть список для фильтра по полу
     * @return array
     */
    public function actionGetSexTags() {
        return ['Женщинам', 'Мужчинам', 'Девочкам', 'Мальчикам'];
    }

    const actionGetAppBarTree = 'GET /v1/ref-blank-sex/get-app-bar-tree';

    /**
     * Построить дерево для head сайта
     * @return array
     */
    public function actionGetAppBarTree()
    {
        $sexObjects = [
            [
                'tag' => 'Женщинам',
                'groups' => []
            ],
            [
                'tag' => 'Мужчинам',
                'groups' => []
            ],
            [
                'tag' => 'Детям',
                'groups' => []
            ]
        ];


        $sexObjectToRealSexIDs = [
            // Женщинам
            0 => [2, 5],
            // Мужчинам
            1 => [1, 5],
            // Детям
            2 => [3, 4, 6]
        ];

        foreach ($sexObjectToRealSexIDs as $sexObjectIndex => $sexIds) {
            $sexObjects[$sexObjectIndex]['groups'] = (new Query())
                ->select('ref_blank_group.*')
                ->orderBy('sort')
                ->distinct()
                ->from('ref_blank_model')
                ->innerJoin('ref_blank_class', 'ref_blank_model.class_fk = ref_blank_class.id')
                ->innerJoin('ref_blank_group', 'ref_blank_class.group_fk = ref_blank_group.id')
                ->innerJoin('ref_blank_sex', 'ref_blank_model.sex_fk = ref_blank_sex.id')
                ->where(['ref_blank_sex.id' => $sexIds])
                ->all();
            foreach ($sexObjects[$sexObjectIndex]['groups'] as &$group) {
                $group['classes'] = (new Query())
                    ->select('ref_blank_class.*')
                    ->distinct()
                    ->from('ref_blank_model')
                    ->innerJoin('ref_blank_class', 'ref_blank_model.class_fk = ref_blank_class.id')
                    ->innerJoin('ref_blank_group', 'ref_blank_class.group_fk = ref_blank_group.id')
                    ->innerJoin('ref_blank_sex', 'ref_blank_model.sex_fk = ref_blank_sex.id')
                    ->where(['ref_blank_sex.id' => $sexIds])
                    ->andWhere(['ref_blank_group.id' => $group['id']])
                    ->groupBy('ref_blank_class.oxouno')
                    ->all();
            }
        }


        return $sexObjects;
    }
}