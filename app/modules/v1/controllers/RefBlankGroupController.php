<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefBlankGroup;
use Yii;
use yii\data\ActiveDataProvider;

class RefBlankGroupController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankGroup';

    const actionGet = 'GET /v1/ref-blank-group/get';

    /**
     * @param $id
     * @return \app\modules\v1\classes\ActiveRecordExtended
     */
    public function actionGet($id)
    {
        return RefBlankGroup::get($id);
    }

    const actionGetBaseTree = 'GET /v1/ref-blank-group/get-base-tree';

    /**
     * Вернуть JSON для базового дерева
     */
    public function actionGetBaseTree()
    {

        /** @var $groups RefBlankGroup[] */
        $groups = RefBlankGroup::find()
            ->with('refBlankClassesTree.refBlankModelsTree.sexFk')
            ->with('refBlankClassesTree.refBlankModelsTree.refArtBlanks.fabricTypeFk')
            ->with('refBlankClassesTree.refBlankModelsTree.refArtBlanks.themeFk')
            ->with('refBlankClassesTree.refBlankModelsTree.refArtBlanks.refProductPrints')
            ->with('refBlankClassesTree.refBlankModelsTree.refArtBlanks.refProductPrints.printFk')
            ->orderBy('title')
            ->all();

        $resp = [];

        foreach ($groups as $groupItem) {
            $group = [];
            $group['type'] = 'group';
            $group['id'] = $groupItem->id;
            $group['title'] = $groupItem->title;
            $group['children'] = [];

            $_groupChildren = [];
            foreach ($groupItem->refBlankClassesTree as $classItem) {
                $class = [];
                $class['type'] = 'class';
                $class['id'] = $classItem->id;
                $class['title'] = $classItem->title;
                $class['children'] = [];

                $_classChildren = [];
                foreach ($classItem->refBlankModelsTree as $modelItem) {
                    $model = [];
                    $model['id'] = $modelItem->id;
                    $model['type'] = 'model';
                    $fashion = $modelItem->fashion ? $modelItem->fashion : '--------------------------';
                    $model['title'] = $modelItem->sexFk->code_ru . ': ' . $fashion .
                        ' ('. $modelItem->title . ')';
                    $model['children'] = [];

                    $_modelChildren = [];
                    foreach ($modelItem->refArtBlanks as $prodItem) {
                        $prod = [];
                        $prod['id'] = $prodItem->id;
                        $prod['type'] = 'prod';
                        $prod['title'] =
                            'OXO-' . str_pad($prodItem->id, 4, '0', STR_PAD_LEFT) .
                            ' (' . $prodItem->fabricTypeFk->type . ' / ' . $prodItem->themeFk->title . ')';
                        $prod['children'] = [];

                        $_prodChildren = [];
                        foreach ($prodItem->refProductPrints as $postprodItem) {
                            $postprod = [];
                            $postprod['id'] = $postprodItem->id;
                            $postprod['type'] = 'postprod';
                            $postprod['title'] =
                                'OXO-' . str_pad($prodItem->id, 4, '0', STR_PAD_LEFT) .
                                '-' . str_pad($postprodItem->id, 3, '0', STR_PAD_LEFT) .
                                ' (' . $postprodItem->printFk->title . ')';
                            $postprod['children'] = [];
                            $_prodChildren[] = $postprod;
                        }
                        $prod['children'] = $_prodChildren;
                        $_modelChildren[] = $prod;
                    }
                    $model['children'] = $_modelChildren;
                    $_classChildren[] = $model;
                }
                $class['children'] = $_classChildren;
                $_groupChildren[] = $class;
            }

            $group['children'] = $_groupChildren;
            $resp[] = $group;
        }

        return $resp;
    }


}