<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefBlankClass;

class RefBlankClassController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankClass';

    const actionGet = 'GET /v1/ref-blank-class/get';

    /**
     * @param $id
     * @return \app\modules\v1\classes\ActiveRecordExtended
     */
    public function actionGet($id)
    {
        return RefBlankClass::get($id);
    }

    const actionGetClassesGroupType = 'GET /v1/ref-blank-class/get-classes-group-type';

    /**
     * Список наименований c группировкой по типу
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetClassesGroupType()
    {

        $resp = [
            'Верх' => [],
            'Костюм' => [],
            'Низ' => [],
        ];

        foreach ($resp as $type => $arr) {
            $data = RefBlankClass::find()
                ->where(['type' => $type])
                ->orderBy('title')
                ->groupBy('oxouno')
                ->all();
            $resp[$type] = $data;
        }

        return $resp;
    }

    const actionGetTags = 'GET ' . '/v1/ref-blank-class/get-tags';

    /**
     * Вернуть список тэгов
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetTags()
    {
        return RefBlankClass::find()
            ->select(['tag'])
            ->orderBy('tag')
            ->groupBy('tag')
            ->all();
    }

    const actionGetAll = 'GET /v1/ref-blank-class/get-all';

    /**
     * Вернуть список наименований v2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll() {
        return RefBlankClass::find()->orderBy('title')->all();
    }
}