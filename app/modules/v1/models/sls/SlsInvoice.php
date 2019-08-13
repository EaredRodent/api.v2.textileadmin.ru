<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsInvoice;
use app\modules\AppMod;
use Yii;
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
            'userFk',
            'attachment' => function () {
                $files = [];
                $pathToFiles = realpath(Yii::getAlias(AppMod::prmPathToSlsMailAttachments));
                $invoiceId = $this->id;
                $mask = $pathToFiles . "/{$invoiceId}-*.*";
                $names = glob($mask);
                foreach ($names as $path) {
                    $files[] = \basename($path);
                }
                return $files;
            },
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
    public static function readSortDown($state, $userId, $sortPos)
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
    public static function readSortItem($state, $userId, $sortPos)
    {
        return self::find()
            ->where(['user_fk' => $userId, 'state' => $state, 'sort' => $sortPos])
            ->one();
    }

    /**
     * @param $state
     * @param int $userId
     * @return int|string
     */
    public static function calcCount($state, $userId = null)
    {
        return self::find()
            ->where(['state' => $state])
            ->andFilterWhere(['user_fk' => $userId])
            ->count();
    }
}