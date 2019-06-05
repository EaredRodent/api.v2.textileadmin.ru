<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models;


use app\gii\GiiSlsInvoice;
use yii\db\ActiveRecord;

class SlsInvoice extends GiiSlsInvoice
{
    const stateReject = 'reject';
    const stateWait = 'wait';
    const stateAccept = 'accept';
    const statePartPay = 'partPay';
    const stateFullPay = 'fullPay';

    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk'
        ]);
    }

    /**
     * @param $state
     * @param $userId
     * @param $sortPos
     * @return array|ActiveRecord[]|self[]
     */
    public static function getSortDown($state, $userId, $sortPos)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => $state])
            ->andWhere(['>', 'sort', $sortPos])
            ->all();
    }

    /**
     * @param $state
     * @param $userId
     * @param $sortPos
     * @return array|ActiveRecord|self
     */
    public static function getSortItem($state, $userId, $sortPos)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => $state, 'sort' => $sortPos])
            ->one();
    }

    /**
     * Кол-во ожидающих акцептования счетов для определенного юзера
     * @param $userId
     * @return int|string
     */
    public static function waitInvoicesCount($userId)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => self::stateWait])
            ->count();
    }

    /**
     * Кол-во ожидающих акцептования счетов для определенного юзера
     * @param $userId
     * @return int|string
     */
    public static function rejectInvoicesCount($userId)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => self::stateReject])
            ->count();
    }
}