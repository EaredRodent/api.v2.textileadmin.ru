<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;

use app\extension\ProdRest;
use app\extension\Sizes;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankClass;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefBlankTheme;
use app\modules\v1\models\ref\RefFabricType;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\ref\RefWeight;

class RefArtBlankController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefArtBlank';

    const actionGet = 'GET /v1/ref-art-blank/get';

    /**
     * @param $id
     * @return \app\modules\v1\classes\ActiveRecordExtended
     */
    public function actionGet($id)
    {
        return RefArtBlank::get($id);
    }

    const actionGetByFiltersExp = 'GET /v1/ref-art-blank/get-by-filters-exp';

    /**
     * Вернуть все артикулы соответствующие фильтрам
     * @param null $sexIds
     * @param null $groupIds
     * @param null $classTags
     * @param null $themeIds
     * @param null $fabTypeIds
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetByFiltersExp($sexIds = null, $groupIds = null, $classTags = null, $themeIds = null, $fabTypeIds = null)
    {
        $sexIds = $sexIds ? explode(',', $sexIds) : [];
        $sexTitles = [];

        if (in_array(1, $sexIds)) {
            $sexTitles = array_merge($sexTitles,
                ['Женский', 'Унисекс взрослый']);
        }

        if (in_array(2, $sexIds)) {
            $sexTitles = array_merge($sexTitles,
                ['Мужской', 'Унисекс взрослый']);
        }

        if (in_array(3, $sexIds)) {
            $sexTitles = array_merge($sexTitles,
                ['Для мальчиков', 'Для девочек', 'Унисекс детский']);
        }

        $groupIds = $groupIds ? explode(',', $groupIds) : [];
        $classTags = $classTags ? explode(',', $classTags) : [];
        $themeIds = $themeIds ? explode(',', $themeIds) : [];
        $fabTypeIds = $fabTypeIds ? explode(',', $fabTypeIds) : [];

        /** @var RefArtBlank[] $refArtBlanks */
        $refArtBlanks = RefArtBlank::find()
            //->with('modelFk.classFk', 'modelFk.sexFk')
            //->with('fabricTypeFk', 'themeFk')
            //->joinWith('fabricTypeFk')
            ->joinWith('modelFk.sexFk')
            ->joinWith('modelFk.classFk')
            ->joinWith('modelFk.classFk.groupFk')
            ->joinWith('fabricTypeFk')
            ->joinWith('themeFk')
            //->select('ref_art_blank.id, ref_art_blank.fabric_type_fk, ref_fabric_type.struct')
            //->select('ref_fabric_type.struct')
            ->filterWhere(['in', 'ref_blank_sex.title', $sexTitles])
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIds])
            ->andFilterWhere(['in', 'ref_blank_class.tag', $classTags])
            ->andFilterWhere(['in', 'ref_blank_theme.id', $themeIds])
            ->andFilterWhere(['in', 'ref_fabric_type.id', $fabTypeIds])
            ->andWhere(['flag_price' => 1])
            ->all();

        $availableRefBlankTheme = [];
        $availableRefFabricType = [];

        foreach ($refArtBlanks as $refArtBlank) {
            if (!in_array($refArtBlank->theme_fk, $availableRefBlankTheme)) {
                $availableRefBlankTheme[] = $refArtBlank->theme_fk;
            }
            if (!in_array($refArtBlank->fabric_type_fk, $availableRefFabricType)) {
                $availableRefFabricType[] = $refArtBlank->fabric_type_fk;
            }
        }

        $availableRefBlankTheme = RefBlankTheme::find()
            ->where(['id' => $availableRefBlankTheme])
            ->all();

        $availableRefFabricType = RefFabricType::find()
            ->where(['id' => $availableRefFabricType])
            ->all();

        return [
            'refArtBlank' => $refArtBlanks ? $refArtBlanks : [],
            'availableRefBlankTheme' => $availableRefBlankTheme,
            'availableRefFabricType' => $availableRefFabricType
        ];
    }

    const actionGetClientDetail = 'GET /v1/ref-art-blank/get-client-detail';

    /**
     * Вернуть размеры и остатки по складу (для отповых клиентов)
     * @param $id
     * @param int $printId
     * @return array
     */
    public function actionGetClientDetail($id)
    {
        /** @var $prod RefArtBlank */
        $prod = RefArtBlank::get($id);
        $sexType = $prod->calcSizeType();

        $rest = new ProdRest([$id]);
        $weight = RefWeight::readRec($prod->model_fk, $prod->fabric_type_fk);

        $resp = [];
        foreach (Sizes::prices as $fSize => $fPrice) {
            if ($prod->$fPrice > 0) {

                $restVal = $rest->getRestPrint($id, 1, $fSize);
                if ($restVal == 0) {
                    $restStr = '#d4000038';
                } elseif ($restVal > 0 && $restVal <= 10) {
                    $restStr = '#d4d40038';
                } else {
                    $restStr = '#00d40038';
                }

                $resp[] = [
                    // 'fSize' => $fSize,
                    'sizeStr' => Sizes::typeCompare[$sexType][$fSize],
                    'price' => $prod->$fPrice,
                    'rest' => $restStr,
                    'weight' => $weight->$fSize,
                ];
            }
        }

        return $resp;
    }

}