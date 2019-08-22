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
    public function actionGetForModel($id) {

        return RefArtBlank::find()
            ->joinWith('themeFk', false)
            ->where(['model_fk' => $id])
            ->orderBy('fabric_type_fk, ref_blank_theme.title, id')
            ->all();
    }

}