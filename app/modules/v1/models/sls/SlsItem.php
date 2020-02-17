<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 3:15 PM
 */

namespace app\modules\v1\models\sls;


use app\extension\Sizes;
use app\gii\GiiSlsItem;
use app\modules\v1\classes\CardProd;
use app\modules\v1\models\ref\RefProductPrint;

class SlsItem extends GiiSlsItem
{
    const selectSumParams = [
        '{{sls_item}}.*',
        '{{ref_blank_group}}.code as group_code',
        '{{ref_blank_class}}.id as classId',
    ];

    public function fields()
    {
        return array_merge(parent::fields(), [
            'cardProd' => function () {
                $prod = $this->blankFk;
                $postProd = null;

                if($this->print_fk !== 1) {
                    $postProd = RefProductPrint::find()
                        ->where(['blank_fk' => $this->blank_fk])
                        ->andWhere(['print_fk' => $this->print_fk])
                        ->one();
                }
                return new CardProd($postProd ? $postProd : $prod);
            }
        ]);
    }

    /**
     * Вернуть резерв
     * @param $prodIds
     * @return self[]
     */
    public static function readReserv($prodIds = null)
    {
        return self::find()
            ->select(array_merge(['{{sls_item}}.*'], Sizes::selectSum))
            ->joinWith(['orderFk'], false)
            ->where(['NOT IN', 'sls_order.status', [SlsOrder::s7_send, SlsOrder::s0_del, SlsOrder::s0_preorder]])
            ->andFilterWhere(['blank_fk' => $prodIds])
            ->groupBy('blank_fk, print_fk, pack_fk')
            ->all();
    }

    public function hPrice3($size)
    {
        $part = explode('_', $size)[1];
        $price = "price_{$part}";
        if ($this->$price === null) {
            return '';
        }
        $num = round($this->$price);
        if ($num > 0) {
            return "{$num}";
        } else {
            return 0;
        }
    }

    /**
     * Итоги по продуктам с принтом для прайса
     * @param $dateSql
     * @return array|\app\modules\sls\ar\SlsItem[]
     */
    public static function readRestProdsPrintForPrice()
    {
        return self::find()
            // ->select(self::selectSumParams)
            ->select(array_merge(self::selectSumParams, Sizes::selectSum))
            ->joinWith('blankFk.modelFk.classFk.groupFk', false)
            // ->joinWith('orderFk', false)
            // ->where('print_fk != 1')
            // ->andWhere('sls_order.status != :s_done', [':s_done' => SlsOrder::s7_send])
            // ->andWhere('sls_order.status != :s_del', [':s_del' => SlsOrder::s0_del])
            // ->andWhere('sls_order.status != :s_pre', [':s_pre' => SlsOrder::s0_preorder])

            ->joinWith(['orderFk'], false)
            ->where(['NOT IN', 'sls_order.status', [SlsOrder::s7_send, SlsOrder::s0_del, SlsOrder::s0_preorder]])
            ->andWhere(['sls_order.flag_return' => 0])
            ->orWhere(['>', 'pre_order_id', 0])
            // ->orWhere(['sls_request.state' => SlsRequest::stateInwork])
            ->andWhere('print_fk != 1')
            ->groupBy('blank_fk, print_fk')
            ->asArray()
            ->all();
    }

    /**
     * Итоги по продуктам без принта для прайса
     * @param $dateSql
     * @return array|\app\modules\sls\ar\SlsItem[]
     */
    public static function readRestProdsForPrice()
    {
        return self::find()
            // ->select(self::selectSumParams)
            ->select(array_merge(self::selectSumParams, Sizes::selectSum))
            ->joinWith('blankFk.modelFk.classFk.groupFk', false)
            // ->joinWith('orderFk', false)
            // ->where(['print_fk' => 1])
            // ->andWhere('sls_order.status != :s_done', [':s_done' => SlsOrder::s7_send])
            // ->andWhere('sls_order.status != :s_del', [':s_del' => SlsOrder::s0_del])
            // ->andWhere('sls_order.status != :s_pre', [':s_pre' => SlsOrder::s0_preorder])

            ->joinWith(['orderFk'], false)
            ->where(['NOT IN', 'sls_order.status', [SlsOrder::s7_send, SlsOrder::s0_del, SlsOrder::s0_preorder]])
            ->andWhere(['sls_order.flag_return' => 0])
            ->orWhere(['>', 'pre_order_id', 0])
            // ->orWhere(['sls_request.state' => SlsRequest::stateInwork])
            ->andWhere(['print_fk' => 1])
            ->groupBy('blank_fk')
            ->asArray()
            ->all();
    }
}