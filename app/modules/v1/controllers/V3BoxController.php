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
use app\modules\v1\models\v3\V3MoneyEvent;
use Yii;
use yii\web\HttpException;

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

    const actionGetForCashier = 'GET /v1/v3-box/get-for-cashier';

    public function actionGetForCashier($boxID = 'FromCurrentUser')
    {
        if ((!YII_ENV_DEV) && ($boxID !== 'FromCurrentUser')) {
            throw new HttpException(200, 'Forbidden.', 200);
        }

        /** @var V3Box $box */
        $box = null;

        if ($boxID === 'FromCurrentUser') {
            $userID = Yii::$app->getUser()->getIdentity()->getId();
            $box = V3Box::findOne(['user_fk' => $userID]);
        } else {
            $box = V3Box::findOne(['id' => $boxID]);
        }

        return $box;
    }

}