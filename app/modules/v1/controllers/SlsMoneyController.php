<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\SlsMoney;

class SlsMoneyController extends ActiveControllerExtended
{
    /** @var SlsMoney $modelClass */
    public $modelClass = 'app\modules\v1\models\SlsMoney';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    function actionGetOut($month = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }
        $resp = $this->modelClass::readOutMoney($month);
        return $resp;
    }

    function actionGetIncom($month = null, $clientId = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }

        $dateStart = "{$month}-01";
        $dateEnd = date("Y-m-t", strtotime($dateStart));
        $dateStartSql = date('Y-m-d 00:00:00', strtotime($dateStart));
        $dateEndSql = date('Y-m-d 23:59:59', strtotime($dateEnd));

        return $this->modelClass::find()
            ->joinWith('orderFk')
            ->with('orderFk.clientFk')
            ->where(['>=', 'ts_incom', $dateStartSql])
            ->andWhere(['<=', 'ts_incom', $dateEndSql])
            ->andWhere(['direct' => $this->modelClass::directIn])
            ->andFilterWhere(['sls_order.client_fk' => $clientId])
            ->orderBy('ts_incom')
            ->all();

    }

}