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

    const actionGetForGroup = 'GET /v1/ref-blank-class/get-for-group';

    /**
     * Вернутьсписок найменований для заданной группы
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForGroup($id) {

        return RefBlankClass::find()->where(['group_fk' => $id])->orderBy('title')->all();
    }

}