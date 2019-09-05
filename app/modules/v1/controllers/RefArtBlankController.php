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


    const actionGetProps = 'GET /v1/ref-art-blank/get-props';

    /**
     * Вернуть параметры продукта для отображения в стравочнике
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetProps($id)
    {

        /** @var $prod RefArtBlank */
        $prod = RefArtBlank::find()
            ->where(['id' => $id])
            ->one();

        $resp = [];

        // Термобелье комплект: для девочек. РЕГЛАН (Нэко-13 134) Арт:
        // OXO-0154

        $resp['title'] =
            $prod->modelFk->classFk->title . ' ' .
            $prod->modelFk->title . ':' . $prod->modelFk->sexFk->title;
        $resp['art'] = $prod->hArt();

        return $resp;
    }

    const actionGetByFiltersExp = 'GET /v1/ref-art-blank/get-by-filters-exp';

    /**
     * Вернуть все артикулы соответствующие фильтрам
     * @param null $sexIds
     * @param null $groupIds
     * @param null $classTags
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

    const actionGetAllExp = 'GET /v1/ref-art-blank/get-all-exp';

    /**
     * Эеспериментальынй экшн потом удалить
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAllExp()
    {
        // todo

        $resp = RefArtBlank::find()
            //->with('modelFk.classFk', 'modelFk.sexFk')
            //->with('fabricTypeFk', 'themeFk')
            ->where(['id' => 100])
            //->asArray()
            ->all();
        return $resp;
    }

    const actionGetClientDetail = 'GET /v1/ref-art-blank/get-client-detail';

    /**
     * Вернуть размеры и остатки по складу (для отповых клиентов)
     * @param $id
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