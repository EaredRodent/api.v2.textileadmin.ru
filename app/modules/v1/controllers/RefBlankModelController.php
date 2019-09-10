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
use app\modules\v1\models\ref\RefBlankModel;

class RefBlankModelController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankModel';

    const actionGet = 'GET /v1/ref-blank-model/get';

    /**
     * @param $id
     * @return \app\modules\v1\classes\ActiveRecordExtended
     */
    public function actionGet($id)
    {
        return RefBlankModel::get($id);
    }

    const actionGetForClass = 'GET /v1/ref-blank-model/get-for-class';

    /**
     * Вернуть список моделей для заданного наименования (класса)
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForClass($id)
    {

        return RefBlankModel::find()->where(['class_fk' => $id])->orderBy('sex_fk, title')->all();
    }

}