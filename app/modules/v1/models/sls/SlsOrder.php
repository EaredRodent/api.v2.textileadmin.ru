<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:25
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsOrder;
use yii\db\ActiveRecord;

class SlsOrder extends GiiSlsOrder
{
    const s0_del = 's0_del';
    const s0_preorder = 's0_preorder';
    const s1_prep = 's1_prep';
    const s1_wait_assembl = 's1_wait_assembl';
    const s5_assembl = 's5_assembl';
    const s2_wait = 's2_wait';
    const s3_accept = 's3_accept';
    const s4_reject = 's4_reject';
    const s6_allow = 's6_allow';
    const s7_send = 's7_send';

    const payBank = 'bank';
    const payCash = 'cash';

    public function fields()
    {
        return array_merge(parent::fields(), [
            'clientFk'
        ]);
    }

    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @param null $payType
     * @return array|ActiveRecord[]|self[]
     */
    public static function getForReport($dateStart = null, $dateEnd = null, $payType = null)
    {
        return self::find()
            ->where(['!=', 'status', self::s0_del])
            ->filterWhere(['pay_type' => $payType])
            ->andFilterWhere(['>=', $payType === self::payBank ? 'ts_doc' : 'ts_assembl', $dateStart])
            ->andFilterWhere(['<=', $payType === self::payBank ? 'ts_doc' : 'ts_assembl', $dateEnd])
            ->all();
    }
}