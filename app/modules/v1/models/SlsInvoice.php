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

    /**
     * @return array|false
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk'
        ]);
    }

    /**
     * @return array|ActiveRecord[]|self[]
     */
    public static function getAccept()
    {
        return self::find()
            ->where(['state' => self::stateAccept])
            ->orderBy('sort')
            ->all();
    }

    /**
     * @return array|ActiveRecord[]|self[]
     */
    public static function getPartPay()
    {
        return self::find()
            ->where(['state' => self::statePartPay])
            ->orderBy('sort')
            ->all();
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
     * @return array|ActiveRecord|null|self
     */
    public static function getSortItem($state, $userId, $sortPos)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => $state, 'sort' => $sortPos])
            ->one();
    }

    /**
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
     * @param $userId
     * @return int|string
     */
    public static function rejectInvoicesCount($userId)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => self::stateReject])
            ->count();
    }

    /**
     * @return int|string
     */
    public static function acceptInvoicesCount()
    {
        return self::find()
            ->where(['state' => SlsInvoice::stateAccept])
            ->count();
    }
}