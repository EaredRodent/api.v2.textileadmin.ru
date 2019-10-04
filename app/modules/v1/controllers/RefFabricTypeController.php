<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 04.09.2019
 * Time: 16:42
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefFabricType;

class RefFabricTypeController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefFabricType';

    const actionGetFabricTypes = 'GET /v1/ref-fabric-type/get-fabric-types';

    /**
     * Вернуть список для фильтра по ткани
     * @return RefFabricType[]
     */
    public function actionGetFabricTypes() {
        return RefFabricType::getAll();
    }
}