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

class RefBlankGroupController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankGroup';

    const actionGet = 'GET /v1/ref-blank-group/get';

    /**
     * Вернуть группы изделий в алфавитном порядке
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGet()
    {
        return RefBlankGroup::find()->orderBy('title')->all();
    }
}