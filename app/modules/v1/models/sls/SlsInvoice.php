<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsInvoice;
use app\models\AnxUser;
use app\modules\AppMod;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class SlsInvoice
 * @package app\modules\v1\models\sls
 * @property int $sum_rest
 */
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
            'typeFk',
            'attachment' => function () {
                $files = [];
                $alias = AppMod::filesRout[AppMod::filesInvoiceAttachement];
                $pathToFiles = realpath(Yii::getAlias($alias));
                $invoiceId = $this->id;
                $mask = $pathToFiles . "/{$invoiceId}-*.*";
                $names = glob($mask);

                /** @var AnxUser $contact */
                $contact = Yii::$app->getUser()->getIdentity();
                $urlKey = $contact->url_key;

                foreach ($names as $path) {
                    $baseName = basename($path);
                    $files[] = CURRENT_API_URL . "/v1/files/get/{$urlKey}/filesInvoiceAttachement/{$baseName}";
                }
                return $files;
            },
            'expired' => function () {
                if (!$this->ts_pay) {
                    return false;
                }
                return date('Y-m-d 23:59:59', strtotime($this->ts_pay)) < date('Y-m-d H:i:s');
            }
        ]);
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['sum_rest']);
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

    /**
     * @return float
     */
    public static function calcSummWait()
    {
        /** @var $rec self */
        $rec = SlsInvoice::find()
            ->select(['SUM(summ) as summ'])
            ->where(['state' => SlsInvoice::stateWait])
            ->groupBy('state')
            ->one();
        return ($rec) ? $rec->summ : 0;
    }

    /**
     * @return float
     */
    public static function calcSummPartPay()
    {
        /** @var $rec self */
        $rec = SlsInvoice::find()
            ->select(['(SUM(summ) - SUM(summ_pay)) as summ'])
            ->where(['state' => SlsInvoice::statePartPay])
            ->groupBy('state')
            ->one();
        return ($rec) ? $rec->summ : 0;
    }

    /**
     * @return float
     */
    public static function calcSummAccept()
    {
        /** @var $rec self */
        $rec = SlsInvoice::find()
            ->select(['(SUM(summ) - SUM(summ_pay)) as summ'])
            ->where(['state' => SlsInvoice::stateAccept])
            ->groupBy('state')
            ->one();
        return ($rec) ? $rec->summ : 0;
    }
}