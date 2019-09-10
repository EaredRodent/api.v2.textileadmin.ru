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

    const actionGetForGroupSex = 'GET /v1/ref-blank-class/get-for-group-sex';

    /**
     * Вернуть список наименований для определенных sexId и groupId
     * @param $sexId
     * @param $groupId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForGroupSex($sexId, $groupId)
    {

        $resp = RefBlankClass::find()
            ->joinWith('refBlankModels')
            ->where(['sex_fk' => $sexId])
            ->andWhere(['group_fk' => $groupId])
            ->orderBy('title')
            ->all();
        return $resp;
    }

    const actionGetClassesExp = 'GET /v1/ref-blank-class/get-classes-exp';

    /**
     * Список наименований
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetClassesExp()
    {
        $resp = RefBlankClass::find()
            ->orderBy('title')
            ->groupBy('tag')
            ->all();
        return $resp;
    }

}