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

    const actionGetForModel = 'GET /v1/ref-art-blank/get-for-model';

    /**
     * Вернуть список изделий для заданной модели
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForModel($id)
    {

        return RefArtBlank::find()
            ->joinWith('themeFk', false)
            ->where(['model_fk' => $id])
            ->orderBy('fabric_type_fk, ref_blank_theme.title, id')
            ->all();
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
                    $restStr = '#d4000018';
                } elseif ($restVal > 0 && $restVal <= 10) {
                    $restStr = '#d4d40018';
                } else {
                    $restStr = '#00d40018';
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

    const actionGetByFilters = 'GET /v1/ref-art-blank/get-by-filters';

    /**
     * Вернуть все артикулы соответствующие фильтрам
     * @param null $sexTags
     * @param null $groupIDs
     * @param null $classTags
     * @param null $themeIDs
     * @param null $fabTypeIDs
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetByFilters($sexTags = null, $groupIDs = null, $classTags = null, $themeIDs = null, $fabTypeIDs = null)
    {
        $sexTags = $sexTags ? explode(',', $sexTags) : [];
        $sexTitles = [];

        if (in_array('Женщинам', $sexTags)) {
            $sexTitles = array_merge($sexTitles,
                ['Женский', 'Унисекс взрослый']);
        }

        if (in_array('Мужчинам', $sexTags)) {
            $sexTitles = array_merge($sexTitles,
                ['Мужской', 'Унисекс взрослый']);
        }

        if (in_array('Детям', $sexTags)) {
            $sexTitles = array_merge($sexTitles,
                ['Для мальчиков', 'Для девочек', 'Унисекс детский']);
        }

        $groupIDs = $groupIDs ? explode(',', $groupIDs) : [];
        $classTags = $classTags ? explode(',', $classTags) : [];
        $themeIDs = $themeIDs ? explode(',', $themeIDs) : [];
        $fabTypeIDs = $fabTypeIDs ? explode(',', $fabTypeIDs) : [];

        /** @var RefArtBlank[] $filteredProds */
        $filteredProds = RefArtBlank::find()
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
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIDs])
            ->andFilterWhere(['in', 'ref_blank_class.tag', $classTags])
            ->andFilterWhere(['in', 'ref_blank_theme.id', $themeIDs])
            ->andFilterWhere(['in', 'ref_fabric_type.id', $fabTypeIDs])
            ->andWhere(['flag_price' => 1])
            ->all();

        // Ignore theme and fabric type

        /** @var RefArtBlank[] $filteredProds2 */
        $filteredProds2 = RefArtBlank::find()
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
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIDs])
            ->andFilterWhere(['in', 'ref_blank_class.tag', $classTags])
            ->andWhere(['flag_price' => 1])
            ->all();

        $availableRefBlankTheme = [];
        $availableRefFabricType = [];

        foreach ($filteredProds2 as $prod) {
            if (!in_array($prod->theme_fk, $availableRefBlankTheme)) {
                $availableRefBlankTheme[] = $prod->theme_fk;
            }
            if (!in_array($prod->fabric_type_fk, $availableRefFabricType)) {
                $availableRefFabricType[] = $prod->fabric_type_fk;
            }
        }

        $availableRefBlankTheme = RefBlankTheme::find()
            ->where(['id' => $availableRefBlankTheme])
            ->all();

        $availableRefFabricType = RefFabricType::find()
            ->where(['id' => $availableRefFabricType])
            ->all();

        return [
            'filteredProds' => $filteredProds ? $filteredProds : [],
            'availableRefBlankTheme' => $availableRefBlankTheme,
            'availableRefFabricType' => $availableRefFabricType
        ];
    }
}