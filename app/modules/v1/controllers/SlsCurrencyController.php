<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11.06.2019
 * Time: 14:04
 */

namespace app\modules\v1\controllers;


use app\models\SlsCurrency;
use app\modules\v1\classes\ActiveControllerExtended;

class SlsCurrencyController extends ActiveControllerExtended
{
    public $modelClass = 'app\models\SlsCurrency';

    /**
     * TODO
     * @return array
     */
    public function actionGetLast()
    {
        $res = [];
        $usd = SlsCurrency::find()
            ->where(['unit' => SlsCurrency::USD])
            ->orderBy(['date' => SORT_DESC])
            ->limit(1)
            ->asArray()
            ->one();
        if ($usd) {
            $res[] = $usd;
        }
        $eur = SlsCurrency::find()
            ->where(['unit' => SlsCurrency::EUR])
            ->orderBy(['date' => SORT_DESC])
            ->limit(1)
            ->asArray()
            ->one();
        if ($eur) {
            $res[] = $eur;
        }
        return $res;
    }
}