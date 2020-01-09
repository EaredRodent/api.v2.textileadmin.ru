<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 1/9/2020
 * Time: 6:02 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefProdPack;

class RefProdPackController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefProdPack';

    const actionGetAll = 'GET /v1/ref-prod-pack/get-all';

    /**
     * Вернуть список упаковок v2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll() {
        return RefProdPack::find()->orderBy('title')->all();
    }
}