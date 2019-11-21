<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/21/2019
 * Time: 12:36 PM
 */

namespace app\commands\schedule\tasks;


use app\modules\AppMod;
use app\modules\v1\models\sls\SlsOrder;
use WebSocket\Client;
use yii\web\HttpException;

class OrderCleaner
{
    public function init()
    {
        date_default_timezone_set('Europe/Moscow');

        /** @var SlsOrder[] $orders */
        $orders = SlsOrder::find()
            ->where(['status' => SlsOrder::s1_client_prep])
            ->andWhere(['<=', 'ts_expire', date('Y-m-d H:i:s', time())])
            ->all();

        foreach ($orders as $order) {
            $order->status = SlsOrder::s0_del;
            if (!$order->save()) {
                $orderID = $order->id;
                $newOrderStatus = SlsOrder::s0_del;
                throw new HttpException(500, "OrderCleaner: Can not save order ${$orderID} with status ${$newOrderStatus}");
            }
        }

        if (count($orders)) {
            $wsc = new Client(AppMod::wssUrl);
            try {
                $wsc->send(json_encode([
                    'secret_key' => AppMod::wsSenderSecretKey,
                    'message' => [SlsOrder::tableName()]
                ]));
            } catch (\Exception $ee) {
            }
        }
    }
}