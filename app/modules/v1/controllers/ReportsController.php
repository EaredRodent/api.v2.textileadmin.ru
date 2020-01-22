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
use app\modules\v1\models\sls\SlsBalanceParam;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsPreorder;
use app\modules\v1\models\v3\V3Box;
use app\modules\v1\models\v3\V3Invoice;
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
        $osArr = SlsBalanceParam::find()
            ->where(['type' => 'os'])
            ->all();

        $osSum = 0;

        foreach ($osArr as $os) {
            $osSum += $os->value;
        }

        $active[] = [
            'name' => 'Основные средства',
            'value' => $osSum
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

        // Остатки на счете (нал)

        $sumRestCash = 0;
        /** @var V3Box[] $v3BoxArr */
        $v3BoxArr = V3Box::find()->all();

        foreach ($v3BoxArr as $v3Box) {
            $fff = $v3Box->getBalance();
            $sumRestCash += $fff;
        }

        $active[] = [
            'name' => 'Остатки на счете (нал)',
            'value' => $sumRestCash,
        ];

        // Дебиторская задолженность
        $osv = new MoneyReport(
            date('Y-m-d', strtotime('first day of this month')),
            date('Y-m-d H:i:s'),
            null //SlsMoney::typeBank
        );
        $active[] = [
            'name' => 'Дебиторская задолженность',
            'value' => (float)$osv->itogo['endDebet'],
        ];

        $activeSum = $osSum + $sumStor + $sumFabric +
            (float)$sumComp + (float)$sumRestBankMoney + $sumRestCash + (float)$osv->itogo['endDebet'];


        // Пассив

        $passive = [];

        // Займы
        $loansArr = SlsBalanceParam::find()
            ->where(['type' => 'loans'])
            ->all();

        $loansSum = 0;

        foreach ($loansArr as $loans) {
            $loansSum += $loans->value;
        }

        $passive[] = [
            'name' => 'Займы',
            'value' => $loansSum,
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

        // Кредиторская задолженность (безнал)
        $kredLoad = SlsInvoice::calcSummWait() + SlsInvoice::calcSummPartPay() + SlsInvoice::calcSummAccept();
        $passive[] = [
            'name' => 'Кредиторская задолженность',
            'value' => $kredLoad,
        ];

        // Кредиторская задолженность (нал)

        $kredLoadCash = 0;
        $users = V3Invoice::getPrepForAdmin();

        foreach ($users as $user) {
            foreach ($user as $prepInvoice) {
                $kredLoadCash += $prepInvoice->summ;
            }
        }

        $partPays = V3Invoice::getPartPayForAdmin();

        foreach ($partPays as $partPay) {
            $kredLoadCash += $partPay->summ + $partPay->sum_pay;
        }

        $passive[] = [
            'name' => 'Кредиторская задолженность (нал)',
            'value' => $kredLoadCash,
        ];

        $passiveSum = $loansSum + $summPrepay + $kredLoad + $kredLoadCash;

        // Нераспределенная прибыль
        $passive[] = [
            'name' => 'Нераспределенная прибыль',
            'value' => $activeSum - $passiveSum,
        ];


        return [
            'active' => $active,
            'passive' => $passive,
            'activeTotalMoney' => $activeSum,
            'passiveTotalMoney' => $activeSum
        ];
    }


}