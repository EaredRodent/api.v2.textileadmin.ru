<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:25
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsOrder;
use app\gii\GiiSlsPreorder;
use yii\db\ActiveRecord;

class SlsPreorder extends GiiSlsPreorder
{

    public $count_prods;
    public $summ_order;
    public $summ_pay;
    public $summ_free; // Нераспределенные деньги

    public function fields()
    {
        return array_merge(parent::fields(), [
            'clientFk'
        ]);
    }



    const selectCountProds = '
		(SELECT SUM( 
		  IFNULL(size_5xs, 0) 
		  + IFNULL(size_4xs, 0) 
		  + IFNULL(size_3xs, 0) 
		  + IFNULL(size_2xs, 0) 
		  + IFNULL(size_xs, 0) 
		  + IFNULL(size_s, 0) 
		  + IFNULL(size_m, 0) 
		  + IFNULL(size_l, 0) 
		  + IFNULL(size_xl, 0) 
		  + IFNULL(size_2xl, 0) 
		  + IFNULL(size_3xl, 0) 
		  + IFNULL(size_4xl, 0) 
		  ) FROM sls_preorder_item WHERE sls_preorder_item.preorder_fk = sls_preorder.id) 
		  AS count_prods
	';

    const selectSummOrder = '
		(SELECT SUM( 
		  IFNULL(size_5xs * price_5xs, 0) 
		  + IFNULL(size_4xs * price_4xs, 0) 
		  + IFNULL(size_3xs * price_3xs, 0) 
		  + IFNULL(size_2xs * price_2xs, 0) 
		  + IFNULL(size_xs * price_xs, 0) 
		  + IFNULL(size_s * price_s, 0) 
		  + IFNULL(size_m * price_m, 0) 
		  + IFNULL(size_l * price_l, 0) 
		  + IFNULL(size_xl * price_xl, 0) 
		  + IFNULL(size_2xl * price_2xl, 0) 
		  + IFNULL(size_3xl * price_3xl, 0) 
		  + IFNULL(size_4xl * price_4xl, 0) 
		  ) FROM sls_preorder_item WHERE sls_preorder_item.preorder_fk = sls_preorder.id) 
		  AS summ_order
	';

    const selectSummPay = '
		(SELECT SUM(summ) FROM sls_money WHERE sls_money.preorder_fk = sls_preorder.id AND sls_money.summ > 0) 
		  AS summ_pay
	';

    const selectSummFree = '
		(SELECT SUM(summ) FROM sls_money WHERE sls_money.preorder_fk = sls_preorder.id) 
		  AS summ_free
	';


    /**
     * @param null $state
     * @param null $user
     * @param null $client
     * @param null $typePay
     * @return array|self[]
     */
    public static function readPreorders($state = null, $user = null, $client = null, $typePay = null)
    {
        return self::find()
            ->select(['{{sls_preorder}}.*', self::selectSummOrder, self::selectCountProds,
                self::selectSummPay, self::selectSummFree])
            ->with('clientFk')
            ->andFilterWhere(['state' => $state])
            ->andFilterWhere(['user_fk' => $user])
            ->andFilterWhere(['client_fk' => $client])
            ->andFilterWhere(['pay_type' => $typePay])
            ->orderBy('ts_create DESC')
            ->all();
    }
}