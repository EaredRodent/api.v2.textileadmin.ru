<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\BaseClassTemp;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsMoney;
use app\objects\Prices;
use ReflectionClass;
use Yii;
use yii\web\HttpException;


class ReportsController extends ActiveControllerExtended
{

    public $modelClass = '';

    const actionEnterpriseBalance = 'GET /v1/reports/enterprise-balance';

    /**
     * Вернуть баланс предприятия
     * @return array
     */
    public function actionEnterpriseBalance()
    {
        $response = [];

        // Считать основные средства

        $sum = 0;

        /** @var PrStorProd[] $storProd */
        $storProd = PrStorProd::readRest(null);
        $price = new Prices();

        foreach ($storProd as $prod) {
            foreach (Sizes::prices as $fSize => $fPrice) {
                $curPrice = $price->getPrice($prod->blank_fk, $prod->print_fk, $fSize);
                $sum += $prod->$fSize * round($curPrice * 0.71);
            }
        }


        $response['active'][] = [
            'name' => 'Основные средства',
            'value' => $sum
        ];

        // Считать склад ткани

        $sum = 0;

        return $response;
    }



}