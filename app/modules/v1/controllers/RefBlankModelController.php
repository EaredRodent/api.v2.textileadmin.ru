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
use yii\data\ActiveDataProvider;

class RefBlankModelController extends ActiveControllerExtended
{
    /** @var RefBlankModel $modelClass */
    public $modelClass = 'app\modules\v1\models\ref\RefBlankModel';

    const actionIndex = 'GET /v1/ref-blank-model/index';
    const actionGet = 'GET /v1/ref-blank-model/get';

    /**
     * @param $id
     * @return \app\modules\v1\classes\ActiveRecordExtended
     */
    public function actionGet($id)
    {
        return RefBlankModel::get($id);
    }

    public function actions() {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'indexDataProvider'];
        return $actions;
    }

    public function indexDataProvider() {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
        ]);
    }
}