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
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
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

        if ($order->clientFk->org_fk !== $contact->org_fk) {
            throw new HttpException(200, 'Попытка добавить продукт в заказ созданный на юр.лицо другого клиента.', 200);
        }

        $item = new SlsItem();
        $item->attributes = $form;
        $item->print_fk = 1;
        $item->pack_fk = 1;

        $art = RefArtBlank::findOne(['id' => $item->blank_fk]);

        foreach (Sizes::prices as $size => $price) {
            if ($item->$size) {
                $item->$price = $art->$price;
            }
        }

        if (!$item->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}