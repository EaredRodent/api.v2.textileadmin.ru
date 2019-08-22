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
     * @return ActiveDataProvider
     */
    public function actionGetBaseTree()
    {

        // https://www.yiiframework.ru/forum/viewtopic.php?t=31278

        $arr = RefBlankGroup::readForBaseTree();
        return $arr;

//        $query = RefBlankGroup::find()->with('refBlankClasses');
//        return new ActiveDataProvider([
//            'query' => $query,
//        ]);


    }


}