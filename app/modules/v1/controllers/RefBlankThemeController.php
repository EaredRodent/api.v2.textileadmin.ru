<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 04.09.2019
 * Time: 16:40
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefBlankTheme;

class RefBlankThemeController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\ref\RefBlankTheme';

    const actionGetAll = 'GET /v1/ref-blank-theme/get-all';

    /**
     * Получить список всех цветов
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll()
    {
        return RefBlankTheme::getAll();
    }
}