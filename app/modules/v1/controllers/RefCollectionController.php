<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 4/2/2020
 * Time: 10:03 AM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefCollection;

class RefCollectionController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetAll = 'GET /v1/ref-collection/get-all';

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll()
    {
        return RefCollection::find()
            ->innerJoin('ref_art_blank', 'ref_art_blank.collection_fk = ref_collection.id')
            ->andWhere(['flag_in_price' => 1])
            ->all();
    }
}