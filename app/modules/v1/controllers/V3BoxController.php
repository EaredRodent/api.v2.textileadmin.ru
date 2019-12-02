<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 12/2/2019
 * Time: 11:04 AM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\v3\V3Box;

class V3BoxController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetForAdmin = 'GET /v1/v3-box/get-for-admin';

    public function actionGetForAdmin()
    {
        return V3Box::find()->all();
    }

    const actionCreateEdit = 'POST /v1/v3-box/create-edit';

    public function actionCreateEdit($form)
    {
        $form = json_decode($form, true);
        $box = null;

        if(isset($form['id'])) {
            $box = V3Box::findOne(['id' => $form['id']]);
        } else {
            $box = new V3Box();
        }

        $box->load($form, '');
        $box->save();

        return ['_result_' => 'success'];
    }
}