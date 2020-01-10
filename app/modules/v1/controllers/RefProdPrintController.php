<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 1/9/2020
 * Time: 6:02 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefProdPrint;

class RefProdPrintController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefProdPrint';

    const actionGetAll = 'GET /v1/ref-prod-print/get-all';

    /**
     * Вернуть список принтов v2
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll() {
        return RefProdPrint::find()->orderBy('title')->all();
    }
}