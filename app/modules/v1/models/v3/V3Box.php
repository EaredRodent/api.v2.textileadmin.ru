<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 12/2/2019
 * Time: 11:04 AM
 */

namespace app\modules\v1\models\v3;


use app\gii\GiiV3Box;

class V3Box extends GiiV3Box
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk',
            'balance' => function () {
                return $this->getBalance();
            }
        ]);
    }

    public function getBalance()
    {
        /** @var V3MoneyEvent[] $moneyEvents */
        $moneyEvents = V3MoneyEvent::find()
            ->where(['box_fk' => $this->id])
            ->andWhere(['state' => V3MoneyEvent::state['pay']])
            ->all();

        $balance = 0;

        foreach ($moneyEvents as $moneyEvent) {
            $balance += +$moneyEvent->summ;
        }

        return $balance;
    }
}