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
use app\modules\v1\models\ref\RefProductPrint;

class RefBlankThemeController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankTheme';

    const actionGetThemes = 'GET /v1/ref-blank-theme/get-themes';

    /**
     * Вернуть список для фильтра по цвету
     * @return RefBlankTheme[]
     */
    public function actionGetThemes() {
        $prods = RefArtBlank::find()
            ->where(['flag_price' => 1])
            ->all();

        $prodsPrint = RefProductPrint::find()
            ->where(['flag_price' => 1])
            ->all();

        $themeIDs = [];

        foreach ($prods as $prod) {
            if (!in_array($prod->theme_fk, $themeIDs)) {
                $themeIDs[] = $prod->theme_fk;
            }
        }

        foreach ($prodsPrint as $prodPrint) {
            if (!in_array($prodPrint->blankFk->theme_fk, $themeIDs)) {
                $themeIDs[] = $prodPrint->blankFk->theme_fk;
            }
        }


        return RefBlankTheme::find()
            ->where(['in', 'id', $themeIDs])
            ->orderBy('title_price')
            ->groupBy('title_price')
            ->all();
    }
}