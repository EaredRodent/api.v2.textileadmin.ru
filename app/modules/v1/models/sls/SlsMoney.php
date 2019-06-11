<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:25
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsMoney;

class SlsMoney extends GiiSlsMoney
{
    const typeBank = 'bank';
    const typeCash = 'cash';
    const directIn  = 'in';
    const directOut = 'out';

    public function fields()
    {
        return array_merge(parent::fields(), [
            'orderFk',
            'invoiceFk'
        ]);
    }

    public static function getOutMoney($month = null, $userId = null, $divId = null)
    {
        $dateStartSql = null;
        $dateEndSql = null;

        if ($month !== null) {
            $dateStart = "{$month}-01";
            $dateEnd = date("Y-m-t", strtotime($dateStart));
            $dateStartSql = date('Y-m-d 00:00:00', strtotime($dateStart));
            $dateEndSql = date('Y-m-d 23:59:59', strtotime($dateEnd));
        }

        return self::find()
            ->joinWith('invoiceFk')
            ->with('invoiceFk.userFk')
            ->where(['direct' => self::directOut])
            ->andFilterWhere(['>=', 'ts_incom', $dateStartSql])
            ->andFilterWhere(['<=', 'ts_incom', $dateEndSql])
            ->andFilterWhere(['sls_invoice.user_fk' => $userId])
            ->andFilterWhere(['pay_item_fk' => $divId])
            ->orderBy('ts_incom')
            ->all();
    }
}