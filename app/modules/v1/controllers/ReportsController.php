<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\extension\ProdRest;
use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\BaseClassTemp;
use app\modules\v1\models\comp\CompStor;
use app\modules\v1\models\pr\PrStorFabric;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsPreorder;
use app\objects\MoneyReport;
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

        // Актив

        $active = [];
        
        // Основные средства
        $active[] = [
            'name' => 'Основные средства',
            'value' => 'хз'
        ];

        // Склад готовой продукции
        $sumStor = 0;
        /** @var PrStorProd[] $storProd */
        $storProd = PrStorProd::readRest(null);
        $prices = new Prices();
        foreach ($storProd as $prod) {
            foreach (Sizes::prices as $fSize => $fPrice) {
                $price = $prices->getPrice($prod->blank_fk, $prod->print_fk, $fSize);
                $sumStor += $prod->$fSize * round($price * 0.71);
            }
        }
        $active[] = [
            'name' => 'Склад готовой продукции (29%)',
            'value' => $sumStor,
        ];

        // Склад ткани
        $sumFabric = round(PrStorFabric::readRestTotal()->price);
        $active[] = [
            'name' => 'Склад ткани',
            'value' => $sumFabric,
        ];

        // Склад комплектующих
        $sumComp = 0;
        $skRecs = CompStor::readRests();
        $skPrices = CompStor::calcAvgPrices();
        foreach ($skRecs as $skRec) {
            $skPrice = isset($skPrices[$skRec->item_fk]) ? round($skPrices[$skRec->item_fk], 2) : 0;
            $sumComp += $skPrice * $skRec->count;
        }
        $sumComp = round($sumComp, 2);
        $active[] = [
            'name' => 'Склад комплектующих',
            'value' => (float)$sumComp,
        ];

        // Остатки на счете (безнал)
        $sumRestBankMoney = SlsMoney::calcBalance();
        $active[] = [
            'name' => 'Остатки на счете (безнал)',
            'value' => (float)$sumRestBankMoney,
        ];

        // Дебиторская задолженность
        $osv = new MoneyReport(
            date('Y-m-d', strtotime('first day of this month')),
            date('Y-m-d H:i:s'),
            null //SlsMoney::typeBank
        );
        $active[] = [
            'name' => 'Остатки на счете (безнал)',
            'value' => (float)$osv->itogo['endDebet'],
        ];


        // Пассив

        $passive = [];

        // Займы
        $passive[] = [
            'name' => 'Займы',
            'value' => 'хз',
        ];

        // Предоплаты
        $summPrepay = 0;
        $preOrders = SlsPreorder::readPreorders();
        foreach ($preOrders as $rec) {
            $summPrepay += $rec->summ_free;
        }
        $passive[] = [
            'name' => 'Предоплаты',
            'value' => $summPrepay,
        ];

        // Кредиторская задолженность
        $kredLoad = SlsInvoice::calcSummWait() + SlsInvoice::calcSummPartPay() + SlsInvoice::calcSummAccept();
        $passive[] = [
            'name' => 'Кредиторская задолженность',
            'value' => $kredLoad,
        ];


        return [
            'active' => $active,
            'passive' => $passive,
        ];
    }



}