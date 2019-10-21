<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 4:55 PM
 */

namespace app\modules\v1\controllers;


use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrg;
use Yii;
use yii\web\HttpException;

class SlsItemController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\sls\SlsItem';

    const actionCreateItem = 'POST /v1/sls-item/create-item';

    /**
     * Добавляет продукт в заказ (B2B)
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
        $item->print_fk = 1;
        $item->pack_fk = 1;

        $art = RefArtBlank::findOne(['id' => $item->blank_fk]);

        foreach (Sizes::prices as $size => $price) {
            if (isset($form[$size])) {
                $item->$size = $form[$size];
                $item->$price = $art->$price;
            }
        }

        $legalEntity = SlsClient::findOne(['id' => $order->client_fk]);

        if($legalEntity->discount) {
            $item->discount = $legalEntity->discount;
        } else {
            $org = SlsOrg::findOne(['id' => $legalEntity->org_fk]);
            $item->discount = $org->discount;
        }

        if (!$item->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}