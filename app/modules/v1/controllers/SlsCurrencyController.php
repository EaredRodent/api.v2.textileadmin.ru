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

    const actionGetLast = 'GET /v1/sls-currency/get-last';

    /**
     * @return array
     */
    public function actionGetLast()
    {
        $res = [];
        $date = date('Y.m.d');

        $usd = SlsCurrency::find()
            ->where(['unit' => SlsCurrency::USD])
            ->orderBy(['id' => SORT_DESC])
            ->andWhere(['date' => $date])
            ->one();
        if ($usd) {
            $res[] = $usd;
        }
        $eur = SlsCurrency::find()
            ->where(['unit' => SlsCurrency::EUR])
            ->orderBy(['id' => SORT_DESC])
            ->andWhere(['date' => $date])
            ->one();
        if ($eur) {
            $res[] = $eur;
        }
        return $res;
    }
}