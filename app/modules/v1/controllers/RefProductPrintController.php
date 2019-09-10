<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10.09.2019
 * Time: 14:15
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefProductPrint;

class RefProductPrintController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefProductPrint';

    const actionGet = 'GET /v1/ref-product-print/get';

    /**
     * @param $id
     * @return \app\modules\v1\classes\ActiveRecordExtended
     */
    public function actionGet($id)
    {
        return RefProductPrint::get($id);
    }
}