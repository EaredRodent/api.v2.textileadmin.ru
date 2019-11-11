<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\Helper;
use app\extension\ProdRest;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefEan;
use app\modules\v1\models\ref\RefProductPrint;
use app\objects\Prices;
use app\objects\ProdWeight;

class OxounoApiController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetProductCatalog = 'GET /v1/oxouno-api/get-product-catalog';

    /**
     * Возвращает каталог продукции, ключ ean-13
     * @return array
     */
    public function actionGetProductCatalog()
    {
        /** @var $eans RefEan[] */
        $eans = RefEan::find()
            //->limit(500)
            //->offset(3000)
            ->with('blankFk')
            ->all();

        $prices = new Prices();
        $weight = new ProdWeight();

        $prodArr = [];

        /** @var $prods RefArtBlank[] */
        $prods = RefArtBlank::find()->all();
        foreach ($prods as $prod) {
            $prodArr[$prod->id][1] = $prod;
        }

        /** @var $prodsPost RefProductPrint[] */
        $prodsPost = RefProductPrint::find()->all();
        foreach ($prodsPost as $prodPost) {
            $prodArr[$prodPost->blank_fk][$prodPost->print_fk] = $prodPost;
        }


        $resp = [];

        foreach ($eans as $ean) {

            $item['id'] = $ean->id;
            $item['ean13'] = Helper::getEan13(AppMod::ean13Prefix, $ean->id);
            $item['timeCreate'] = $ean->dt_create;

            /** @var RefArtBlank|RefProductPrint $prodObj  */
            if (isset($prodArr[$ean->blank_fk][$ean->print_fk])) {
                $prodObj = $prodArr[$ean->blank_fk][$ean->print_fk];
            } else {
                continue;
            }

            $item['group'] = $prodObj->fields()['group']();
            $item['class'] = $prodObj->fields()['classOxo']();
            $item['sex'] = $prodObj->fields()['sex']();
            $item['modelId'] = $prodObj->fields()['modelId']();
            $item['modelProdName'] = $prodObj->fields()['modelProdName']();
            $item['modelDescription'] = $prodObj->fields()['modelDescription']();
            $item['modelEpithets'] = $prodObj->fields()['modelEpithets']();
            $item['name'] = $prodObj->fields()['titleStr']();
            $item['color'] = $prodObj->fields()['colorOxo']();
            $item['print'] = $prodObj->fields()['printOxo']();
            $item['article'] = $prodObj->fields()['art']();
            $item['size'] = $ean->size;
            $item['fabric'] = $prodObj->fields()['fabric']();
            $item['fabricDensity'] = $prodObj->fields()['fabricDensity']();
            $item['fabricEpithets'] = $prodObj->fields()['fabricEpithets']();
            $item['fabricCare'] = $prodObj->fields()['fabricCare']();
            $item['flagInPrice'] = $prodObj->fields()['flagInPrice']();
            $item['price'] = $prices->getPrice($ean->blank_fk, $ean->print_fk, $ean->size);
            $item['weight'] = $weight->getWeight(
                $ean->blankFk->model_fk, $ean->blankFk->fabric_type_fk, $ean->size
            );

            $item['photos'] = $prodObj->fields()['photos']();

            $resp[] = $item;
        }


        return $resp;
    }


    const actionGetStorRest = 'GET /v1/oxouno-api/get-stor-rest';

    /**
     * Возвращает каталог продукции, ключ ean-13
     * @return array
     */
    public function actionGetStorRest()
    {
        /** @var $eans RefEan[] */
        $eans = RefEan::find()
            ->all();

        $rest = new ProdRest();

        $resp = [];

        foreach ($eans as $ean) {
            $item['id'] = $ean->id;
            $item['rest'] = $rest->getRestPrint($ean->blank_fk, $ean->print_fk, $ean->size);
            $resp[] =$item;
        }

        return $resp;
    }


}