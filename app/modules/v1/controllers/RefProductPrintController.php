<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10.09.2019
 * Time: 14:15
 */

namespace app\modules\v1\controllers;


use app\extension\ProdRest;
use app\extension\Sizes;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\ref\RefWeight;

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

    const actionGetClientDetail = 'GET /v1/ref-product-print/get-client-detail';

    /**
     * Вернуть размеры и остатки по складу
     * @param $id
     * @param int $printId
     * @return array
     */
    public function actionGetClientDetail($id, $printId = 1)
    {
        /** @var RefProductPrint $postProd */
        $postProd = RefProductPrint::get($id);
        /** @var RefArtBlank $artBlank */
        $artBlank = RefArtBlank::get($postProd->blank_fk);
        $sexType = $artBlank->calcSizeType();

        $rest = new ProdRest([$postProd->blank_fk]);
        $weight = RefWeight::readRec($artBlank->model_fk, $artBlank->fabric_type_fk);

        $resp = [];
        foreach (Sizes::prices as $fSize => $fPrice) {
            if ($postProd->$fPrice > 0) {

                $restVal = $rest->getRestPrint($postProd->blank_fk, $printId, $fSize);
                if ($restVal === 0) {
                    $restStr = '#d4000018';
                } elseif ($restVal > 0 && $restVal <= 10) {
                    $restStr = '#d4d40018';
                } else {
                    $restStr = '#00d40018';
                }

                $resp[] = [
                    // 'fSize' => $fSize,
                    'sizeStr' => Sizes::typeCompare[$sexType][$fSize],
                    'price' => $postProd->$fPrice,
                    'rest' => $restStr,
                    'weight' => $weight->$fSize,
                ];
            }
        }

        return $resp;
    }
}