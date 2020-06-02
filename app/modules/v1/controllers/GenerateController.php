<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 5/29/2020
 * Time: 10:35 AM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\ExcelPrice2;

class GenerateController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionPrice = 'POST /v1/generate/price';

    /**
     * @param array $refArtBlanks [1,2,3,4,5...] - id
     * @param array $refProductPrints [[1, 1], [2, 1], [3, 1]] - id refArtBlank и id refProdPrint
     * @param $mode - ['assort', 'discount']
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \Throwable
     * @throws \yii\web\HttpException
     */
    public function actionPrice(array $refArtBlanks, array $refProductPrints, $mode) {
        $obj = new ExcelPrice2($refArtBlanks, $refProductPrints, $mode, null, null, null);
        return $obj->save();
    }
}