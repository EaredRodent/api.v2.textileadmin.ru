<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\SlsOrder;

class SlsOrderController extends ActiveControllerExtended
{
    /** @var SlsOrder $modelClass */
    public $modelClass = 'app\modules\v1\models\SlsOrder';

    public function actionGetPrep()
    {
        return $this->modelClass::find()
            ->with('clientFk')
            ->where(['status' => $this->modelClass::s1_prep])
            ->orderBy('ts_create')
            ->all();
    }

    public function actionGetInwork()
    {
        return $this->modelClass::find()
            ->with('clientFk')
            ->where(['status' => [
                $this->modelClass::s1_wait_assembl,
                $this->modelClass::s5_assembl,
                $this->modelClass::s2_wait,
                $this->modelClass::s3_accept,
                $this->modelClass::s4_reject,
                $this->modelClass::s6_allow,
            ]])
            ->orderBy('ts_create')
            ->all();
    }

    public function actionGetSend($month = null, $clientId = null)
    {
        $month = ($month == null) ? date("Y-m") : $month;

        $dateStart = "{$month}-01";
        $dateEnd = date("Y-m-t", strtotime($dateStart));
        $dateStartSql = date('Y-m-d 00:00:00', strtotime($dateStart));
        $dateEndSql = date('Y-m-d 23:59:59', strtotime($dateEnd));

        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => $this->modelClass::s7_send])
            ->andWhere(['>=', 'ts_send', $dateStartSql])
            ->andWhere(['<=', 'ts_send', $dateEndSql])
            ->andWhere(['flag_return' => 0])
            ->andFilterWhere(['client_fk' => $clientId])
            ->orderBy('ts_send')
            ->all();
    }


}