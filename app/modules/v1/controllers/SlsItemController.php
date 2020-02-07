<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 4:55 PM
 */

namespace app\modules\v1\controllers;


use app\extension\ProdRest;
use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\log\LogEvent;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrg;
use app\objects\PayReport;
use app\objects\Prices;
use Yii;
use yii\web\HttpException;

class SlsItemController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\sls\SlsItem';

    const actionCreateItem = 'POST /v1/sls-item/create-item';

    /**
     * Добавляет изделие в заказ (B2B)
     * @param $form
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionCreateItem($form)
    {
        $form = json_decode($form, true);

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsOrder $order */
        $order = SlsOrder::get($form['order_fk']);

        if (!$order) {
            throw new HttpException(200, 'Попытка добавить продукт в несуществующий заказ.', 200);
        }

        if ($order->contact_fk !== $contact->id) {
            throw new HttpException(200, 'Попытка добавить продукт в заказ созданный другим пользователем.', 200);
        }

        $item = new SlsItem();
        $item->order_fk = $form['order_fk'];
        $item->blank_fk = $form['blank_fk'];
        $item->print_fk = $form['print_fk'];
        $item->pack_fk = 1;


        $prices = new Prices();

        // todo 29 линий
        // Скидка клиента

        $legalEntity = SlsClient::findOne(['id' => $order->client_fk]);
        if ($legalEntity->discount) {
            $clientDiscount = $legalEntity->discount;
        } else {
            $org = SlsOrg::findOne(['id' => $legalEntity->org_fk]);
            $clientDiscount = $org->discount;
        }

        // Скидка товара

        $prodDiscount = $prices->getDiscount($item->blank_fk, $item->print_fk);

        // Полная скидка

        $totalDiscount = (1 - $clientDiscount / 100) * (1 - $prodDiscount / 100);

        if($totalDiscount < 0.71) {
            $totalDiscount = 0.71;
        }

        $item->discount = (1 - $totalDiscount) * 100;

        $prodRest = new ProdRest([$item->blank_fk]);
        foreach (Sizes::prices as $fSize => $fPrice) {
            if (isset($form[$fSize])) {

                // Отрицательные числа
                if ($form[$fSize] < 0) {
                    throw new HttpException(200, 'Заказ не может быть отрицательным.', 200);
                }

                // Проверка наличия на складе
                $rest = $prodRest->getAvailForOrder($item->blank_fk, $item->print_fk, 1, $fSize);
                if ($rest < $form[$fSize]) {
                    throw new HttpException(200, 'Изделие в таком кол-ве отсутствует на складе.', 200);
                }

                // Запись заказа
                $item->$fSize = $form[$fSize];
                $item->$fPrice = round($prices->getPrice($item->blank_fk, $item->print_fk, $fSize) * $totalDiscount);
            }
        }

        if (!$item->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        LogEvent::log(LogEvent::editOrder);

        return ['_result_' => 'success'];
    }

    const actionEditItem = 'POST /v1/sls-item/edit-item';

    /**
     * Редактирует количество единиц изделия в заказе (B2B)
     * @param $form {"sls_item_id":19926,"size_m":-1,"size_l":1,"size_xl":1,"size_2xl":1}
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionEditItem($form)
    {
        $form = json_decode($form, true);

        $item = SlsItem::findOne(['id' => $form['sls_item_id']]);

        /** @var SlsOrder $order */
        $order = SlsOrder::findOne(['id' => $item->order_fk]);

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        if (!$order) {
            throw new HttpException(200, 'Попытка редактировать продукт в несуществующем заказе.', 200);
        }

        if ($order->contact_fk !== $contact->id) {
            throw new HttpException(200, 'Попытка редактировать продукт в заказе созданным другим пользователем.', 200);
        }

        $prices = new Prices();

        foreach (Sizes::prices as $size => $price) {
            if ($item->$size) {
                $item->$size = 0;
            }
        }

        if (!$item->save()) {
            throw new HttpException(200, 'Внутренняя ошибка #1.', 200);
        }

        // Скидка клиента

        $legalEntity = SlsClient::findOne(['id' => $order->client_fk]);
        if ($legalEntity->discount) {
            $clientDiscount = $legalEntity->discount;
        } else {
            $org = SlsOrg::findOne(['id' => $legalEntity->org_fk]);
            $clientDiscount = $org->discount;
        }

        // Скидка товара

        $prodDiscount = $prices->getDiscount($item->blank_fk, $item->print_fk);

        // Полная скидка

        $totalDiscount = (1 - $clientDiscount / 100) * (1 - $prodDiscount / 100);

        if($totalDiscount < 0.71) {
            $totalDiscount = 0.71;
        }

        $item->discount = (1 - $totalDiscount) * 100;

        // Запись кол-ва в заказе
        $prodRest = new ProdRest([$item->blank_fk]);
        foreach (Sizes::prices as $fSize => $fPrice) {
            if (isset($form[$fSize])) {
                // Отрицательные числа
                if ($form[$fSize] < 0) {
                    throw new HttpException(200, 'Заказ не может быть отрицательным.', 200);
                }
                // Проверка наличия на складе
                $rest = $prodRest->getAvailForOrder($item->blank_fk, $item->print_fk, 1, $fSize);
                if ($rest < $form[$fSize]) {
                    throw new HttpException(200, 'Изделие в таком кол-ве отсутствует на складе.', 200);
                }
                // Запись заказа
                $item->$fSize = $form[$fSize];
                $item->$fPrice = round($prices->getPrice($item->blank_fk, $item->print_fk, $fSize) * $totalDiscount);
            }
        }

        if (!$item->save()) {
            throw new HttpException(200, 'Внутренняя ошибка #2.', 200);
        }

        LogEvent::log(LogEvent::editOrder);

        return ['_result_' => 'success'];
    }

    const actionDeleteItem = 'POST /v1/sls-item/delete-item';

    /**
     * Удаляет продукт из заказа (B2B)
     * @param $form
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionDeleteItem($id)
    {
        $item = SlsItem::findOne(['id' => $id]);

        /** @var SlsOrder $order */
        $order = SlsOrder::findOne(['id' => $item->order_fk]);

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        if (!$order) {
            throw new HttpException(200, 'Попытка удалить продукт из несуществующего заказа.', 200);
        }

        if ($order->contact_fk !== $contact->id) {
            throw new HttpException(200, 'Попытка удалить продукт из заказа созданного другим пользователем.', 200);
        }

        $item->delete();

        LogEvent::log(LogEvent::editOrder);

        return ['_result_' => 'success'];
    }


    const actionGetPayReport = 'GET /v1/sls-item/get-pay-report';

    /**
     * Вернуть отчет по продажам
     * Типы осей
     * @param string $dateStart
     * @param string $dateEnd
     * @param array $articles
     * @param array $sex
     * @param array $groups
     * @param array $fabrics
     * @param array $tags
     * @param array $clients
     * @param array $managers
     * @param string $axisX [month|managerName|groupStr|tag|sexStr]
     * @param string $axisY
     * @param string $resultType [rowMoney|rowCount]
     * @param string $sortType [abc|valDESC|valASC]
     * @return array
     */
    public function actionGetPayReport(
        $dateStart = 'firstDayOfYear',
        $dateEnd = 'currentDay',
        array $articles = [],
        array $sex = [],
        array $groups = [],
        array $fabrics = [],
        array $tags = [],
        array $clients = [],
        array $managers = [],
        $axisX = 'month',
        $axisY = 'managerName',
        $resultType = 'rowMoney',
        $sortType = 'abc'
    )
    {

        $dateStart = ($dateStart === 'firstDayOfYear' || $dateStart === '') ?
            date('Y-01-01') : date('Y-m-d', strtotime($dateStart));

        $dateEnd = ($dateEnd === 'currentDay' || $dateEnd === '') ?
            date('Y-m-d') : date('Y-m-d', strtotime($dateEnd));


        $report = new PayReport(
            $dateStart,
            $dateEnd,
            $articles,
            $sex,
            $groups,
            $fabrics,
            $tags,
            $clients,
            $managers,
            $axisX,
            $axisY,
            $resultType,
            $sortType
        );

        return [
            'axisX' => $report->axisX,
            'axisY' => $report->axisY,
            'matrix' => $report->matrixResult,
            'totalX' => $report->totalX,
            'totalY' => $report->totalY,
            'totalCommon' => $report->totalCommon,
            'totalXAdd' => $report->totalXAdd,
            'totalYAdd' => $report->totalYAdd,
            'totalCommonAdd' => $report->totalCommonAdd,
        ];

    }
}