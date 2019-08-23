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

    const actionGetSort = 'GET /v1/ref-blank-group/get-sort';

    /**
     * Вернуть группы изделий в алфавитном порядке
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetSort()
    {
        return RefBlankGroup::find()->orderBy('title')->all();
    }

    const actionGetTree = 'GET /v1/ref-blank-group/get-tree';

    /**
     * Вернуть JSON для базового дерева
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetTree()
    {
        $arr = [
            [
                'type' => 'group',
                'id' => 1,
                'title' => 'Повседневная одежда',
                'children' => [
                    [
                        'type' => 'class',
                        'id' => 1,
                        'title' => 'Шортики',
                        'children' => [
                            [
                                'type' => 'model',
                                'id' => 1,
                                'title' => 'Короткие',
                                'children' => [
                                    [
                                        'type' => 'prod',
                                        'id' => 123,
                                        'title' => 'OXO-123 (серые / кулирная глать)',
                                        'children' => [],
                                    ],
                                    [
                                        'type' => 'prod',
                                        'id' => 124,
                                        'title' => 'OXO-143 (белые / кулирная глать)',
                                        'children' => [],
                                    ],
                                ],
                            ],

                        ],
                    ],
                    [
                        'type' => 'class',
                        'id' => 2,
                        'title' => 'Футболочки',
                        'children' => [
                            [
                                'type' => 'model',
                                'id' => 1,
                                'title' => 'классик',
                                'children' => [
                                    [
                                        'type' => 'prod',
                                        'id' => 101,
                                        'title' => 'OXO-101 (белая / кулирная глать)',
                                        'children' => [],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'model',
                                'id' => 1,
                                'title' => 'слим',
                                'children' => [
                                    [
                                        'type' => 'prod',
                                        'id' => 111,
                                        'title' => 'OXO-111 (белая / кулирная глать)',
                                        'children' => [
                                            [
                                                'type' => 'postprod',
                                                'id' => 1,
                                                'title' => 'OXO-111-001 (белая + Принт / кулирная глать)',
                                                'children' => [],
                                            ],

                                        ],
                                    ],
                                ],
                            ],

                        ],
                    ],

                ],
            ],
            [
                'type' => 'group',
                'id' => 2,
                'title' => 'Термоодежда',
                'children' => [
                    [
                        'type' => 'class',
                        'id' => 1,
                        'title' => 'теромошорты',
                        'children' => [
                            [
                                'type' => 'model',
                                'id' => 1,
                                'title' => 'Короткие',
                                'children' => [
                                    [
                                        'type' => 'prod',
                                        'id' => 777,
                                        'title' => 'OXO-777 (серые / суперткань)',
                                        'children' => [],
                                    ],
                                ],
                            ],

                        ],
                    ],
                ],
            ],
        ];

        return $arr;
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
                    $model['title'] = $modelItem->title . ':' . $modelItem->sexFk->code_ru;
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