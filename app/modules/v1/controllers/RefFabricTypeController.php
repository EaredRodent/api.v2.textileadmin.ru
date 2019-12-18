<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 04.09.2019
 * Time: 16:42
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefFabricType;
use app\modules\v1\models\ref\RefProductPrint;

class RefFabricTypeController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefFabricType';

    const actionGetFabricTypes = 'GET /v1/ref-fabric-type/get-fabric-types';

    /**
     * Вернуть список для фильтра по ткани
     * @return RefFabricType[]
     */
    public function actionGetFabricTypes() {
        /** @var RefArtBlank[] $prods */
        $prods = RefArtBlank::find()
            ->where(['flag_price' => 1])
            ->all();

        /** @var RefProductPrint[] $prodsPrint */
        $prodsPrint = RefProductPrint::find()
            ->where(['flag_price' => 1])
            ->all();

        $fabTypeIDs = [];

        foreach ($prods as $prod) {
            if (!in_array($prod->fabric_type_fk, $fabTypeIDs)) {
                $fabTypeIDs[] = $prod->fabric_type_fk;
            }
        }

        foreach ($prodsPrint as $prodPrint) {
            if (!in_array($prodPrint->blankFk->fabric_type_fk, $fabTypeIDs)) {
                $fabTypeIDs[] = $prodPrint->blankFk->fabric_type_fk;
            }
        }

        return RefFabricType::find()
            ->where(['in', 'id', $fabTypeIDs])
            ->orderBy('type_price')
            ->groupBy('type_price')
            ->all();
    }


    const actionGetFabricPriceTypes = 'GET /v1/ref-fabric-type/get-fabric-price-types';

    /**
     * Вернуть список для фильтра по ткани для отчета по продажам
     * @return RefFabricType[]
     */
    public function actionGetFabricPriceTypes()
    {
        return RefFabricType::find()
            ->select(['type_price'])
            ->groupBy('type_price')
            ->orderBy('type_price')
            ->all();
    }
}