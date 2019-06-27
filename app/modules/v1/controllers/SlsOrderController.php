<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsOrder;

class SlsOrderController extends ActiveControllerExtended
{
    /** @var SlsOrder $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsOrder';

    const getGetPrep = 'GET /v1/sls-order/get-prep';

    public function actionGetPrep()
    {
        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => SlsOrder::s1_prep])
            ->orderBy('ts_create')
            ->all();
    }

    const getGetInwork = 'GET /v1/sls-order/get-inwork';

    public function actionGetInwork()
    {
        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => [
                SlsOrder::s1_wait_assembl,
                SlsOrder::s5_assembl,
                SlsOrder::s2_wait,
                SlsOrder::s3_accept,
                SlsOrder::s4_reject,
                SlsOrder::s6_allow,
            ]])
            ->orderBy('ts_create')
            ->all();
    }

    const getGetSend = 'GET /v1/sls-order/get-send';

    public function actionGetSend($month = null, $clientId = null)
    {
        $month = ($month == null) ? date("Y-m") : $month;

        $dateStart = "{$month}-01";
        $dateEnd = date("Y-m-t", strtotime($dateStart));
        $dateStartSql = date('Y-m-d 00:00:00', strtotime($dateStart));
        $dateEndSql = date('Y-m-d 23:59:59', strtotime($dateEnd));

        return SlsOrder::find()
            ->with('clientFk')
            ->where(['status' => SlsOrder::s7_send])
            ->andWhere(['>=', 'ts_send', $dateStartSql])
            ->andWhere(['<=', 'ts_send', $dateEndSql])
            ->andWhere(['flag_return' => 0])
            ->andFilterWhere(['client_fk' => $clientId])
            ->orderBy('ts_send')
            ->all();
    }


}