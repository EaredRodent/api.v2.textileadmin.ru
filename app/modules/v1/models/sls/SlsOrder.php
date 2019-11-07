<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:25
 */

namespace app\modules\v1\models\sls;


use app\extension\Sizes;
use app\gii\GiiSlsOrder;
use app\modules\AppMod;
use Yii;
use yii\db\ActiveRecord;

class SlsOrder extends GiiSlsOrder
{
    const s0_del = 's0_del';
    const s0_preorder = 's0_preorder';
    const s1_client_prep = 's1_client_prep';
    const s1_prep = 's1_prep';
    const s1_wait_assembl = 's1_wait_assembl';
    const s5_assembl = 's5_assembl';
    const s2_wait = 's2_wait';
    const s3_accept = 's3_accept';
    const s4_reject = 's4_reject';
    const s6_allow = 's6_allow';
    const s7_send = 's7_send';

    const statuses = [
        self::s0_del => 'Удален',
        self::s0_preorder => 'Предзаказ',
        self::s1_client_prep => 'Подготовка клиентом',
        self::s1_prep => 'Подготовка', // Менеджером
        self::s1_wait_assembl => 'На сборке',
        self::s5_assembl => 'Собран',
        self::s2_wait => 'Акцептование',
        self::s3_accept => 'Акцептован',
        self::s4_reject => 'Отклонен',
        self::s6_allow => 'Разрешен',
        self::s7_send => 'Отгружен',

    ];

    const payBank = 'bank';
    const payCash = 'cash';

    public function fields()
    {
        return array_merge(parent::fields(), [
            'clientFk',
            'contactFk',
            'userFk',
            'statusStr' => function () {
                return self::statuses[$this->status];
            },
            'sum' => function () {
                $slsItems = SlsItem::findAll(['order_fk' => $this->id]);
                $sum = 0;

                foreach ($slsItems as $slsItem) {
                    foreach (Sizes::prices as $size => $price) {
                        if ($slsItem->$size) {
                            $sum += $slsItem->$size * $slsItem->$price;
                        }
                    }
                }

                return $sum;
            },
            'docInvoice' => function () {

                $path = Yii::getAlias(AppMod::pathDocInvoice) . "/invoice-{$this->id}.pdf";
                if (file_exists($path) && $this->contact_fk > 0) {

                    $urlKey = $this->contactFk->url_key;
                    return AppMod::domain . "/v1/files/get-order-doc/{$urlKey}/invoice/{$this->id}";
                } else {
                    return '';
                }
            },
            'docWaybill' => function () {
                $path = Yii::getAlias(AppMod::pathDocWaybill) . "/torg12-{$this->id}.pdf";
                if (file_exists($path) && $this->contact_fk > 0) {
                    $urlKey = $this->contactFk->url_key;
                    return AppMod::domain . "/v1/files/get-order-doc/{$urlKey}/waybill/{$this->id}";
                } else {
                    return '';
                }
            }
        ]);
    }

    /**
     * @param null $dateStartInclusive
     * @param null $dateEnd
     * @param null $payType
     * @return array|ActiveRecord[]|self[]
     */
    public static function getForReport($payType = null, $dateStartInclusive = null, $dateEnd = null)
    {
        /**
         * @param $payType
         * @param $dateStartInclusive
         * @param $dateEnd
         * @return array|ActiveRecord[]|self[]
         */
        $filterOrders = function ($payType, $dateStartInclusive, $dateEnd) {
            return SlsOrder::find()
                ->andFilterWhere(['=', 'pay_type', $payType])
                ->andFilterWhere(['!=', 'status', SlsOrder::s0_del])
                ->andFilterWhere(['>=', $payType === SlsOrder::payBank ? 'ts_doc' : 'ts_assembl', $dateStartInclusive])
                ->andFilterWhere(['<', $payType === SlsOrder::payBank ? 'ts_doc' : 'ts_assembl', $dateEnd])
                ->all();
        };

        $result = [];
        if ($payType) {
            $result = $filterOrders($payType, $dateStartInclusive, $dateEnd);
        } else {
            $result = $filterOrders(self::payBank, $dateStartInclusive, $dateEnd);
            $result = array_merge($result, $filterOrders(self::payCash, $dateStartInclusive, $dateEnd));
        }
        return $result;
    }
}