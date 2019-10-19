<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
use Yii;
use yii\web\HttpException;

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

    const actionGetPrep2 = 'GET /v1/sls-order/get-prep-2';

    /**
     * Получает заказы на подготовке (B2B)
     * @return array|\yii\db\ActiveRecord[]
     * @throws \Throwable
     */
    public function actionGetPrep2()
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsClient[] $legalEntities */
        $legalEntities = SlsClient::findAll(['org_fk' => $contact->org_fk]);
        $legalEntitiesIds = [];

        foreach ($legalEntities as $legalEntity) {
            $legalEntitiesIds[] = $legalEntity->id;
        }

        return SlsOrder::find()
            ->where(['status' => SlsOrder::s1_prep])
            ->andWhere(['client_fk' => $legalEntitiesIds])
            ->all();
    }

    const actionCreateOrder = 'POST /v1/sls-order/create-order';

    /**
     * Создает заказ (B2B)
     * @param $form
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionCreateOrder($form)
    {
        $form = json_decode($form, true);

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsClient $legalEntity */
        $legalEntity = SlsClient::get($form['client_fk']);

        if(!$legalEntity) {
            throw new HttpException(200, 'Попытка добавить заказ на несуществующее юр.лицо.', 200);
        }

        if($legalEntity->org_fk !== $contact->org_fk) {
            throw new HttpException(200, 'Попытка добавить заказ юр.лицо закрепленное за другим клиентом.', 200);
        }

        $order = new SlsOrder();
        $order->attributes = $form;

        if (!$order->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}