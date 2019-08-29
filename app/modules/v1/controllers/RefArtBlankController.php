<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankClass;
use app\modules\v1\models\ref\RefBlankModel;

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
    public function actionGetByFiltersExp($sexIds = null, $groupIds = null, $classTags = null)
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

        return RefArtBlank::find()
            ->with('modelFk.classFk', 'modelFk.sexFk')
            ->with('fabricTypeFk', 'themeFk')
            ->joinWith('fabricTypeFk')
            ->joinWith('modelFk.sexFk')
            ->joinWith('modelFk.classFk')
            ->joinWith('modelFk.classFk.groupFk')
            //->select('ref_art_blank.id, ref_art_blank.fabric_type_fk, ref_fabric_type.struct')
            //->select('ref_fabric_type.struct')
            ->filterWhere(['in', 'ref_blank_sex.title', $sexTitles])
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIds])
            ->andFilterWhere(['in', 'ref_blank_class.tag', $classTags])
            ->all();
    }

    const actionGetAllExp = 'GET /v1/ref-art-blank/get-all-exp';

    public function actionGetAllExp()
    {

    }
}