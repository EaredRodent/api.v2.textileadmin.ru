<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 04.09.2019
 * Time: 16:40
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankTheme;

class RefBlankThemeController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankTheme';

    const actionGetThemes = 'GET /v1/ref-blank-theme/get-themes';

    /**
     * Вернуть список для фильтра по цвету
     * @return RefBlankTheme[]
     */
    public function actionGetThemes() {
        $themeIDs = [];

        $prods = RefArtBlank::find()
            ->where(['flag_price' => 1])
            ->all();

        foreach ($prods as $prod) {
            if (!in_array($prod->theme_fk, $themeIDs)) {
                $themeIDs[] = $prod->theme_fk;
            }
        }


        return RefBlankTheme::findAll(['id' => $themeIDs]);
    }
}