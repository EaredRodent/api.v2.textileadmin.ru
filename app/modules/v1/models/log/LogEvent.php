<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 5:13 PM
 */

namespace app\modules\v1\models\log;


use app\models\AnxUser;
use app\models\GiiLogEvent;
use Yii;
use yii\web\HttpException;

class LogEvent extends GiiLogEvent
{
    const login = 'Login';
    const filterCatalog = 'FilterCatalog';
    const createOrder = 'CreateOrder';
    const editOrder = 'EditOrder';
    const commitOrder = 'CommitOrder';

    const eventStr = [
        self::login => 'Авторизация',
        self::filterCatalog => 'Фильтрация каталога',
        self::createOrder => 'Создание заказа',
        self::editOrder => 'Формирование заказа',
        self::commitOrder => "Оформление заказа \u{2705}",
    ];

    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk',
            'eventStr' => function () {
                return self::eventStr[$this->event];
            },
            'paramsStr' => function () {
                if ($this->event === self::createOrder) {
                    return $this->createOrderTranslate();
                }
                if ($this->event === self::commitOrder) {
                    return $this->commitOrderTranslate();
                }
                return $this->params;
            }
        ]);
    }

    private function createOrderTranslate()
    {
        $params = json_decode($this->params, true);
        return 'Заказ №' . $params['id'];
    }

    private function commitOrderTranslate()
    {
        $params = json_decode($this->params, true);
        return 'Заказ №' . $params['id'] . ' на сумму ' . $params['summ_order'];
    }

    /**
     * @param string $event
     * @param $params
     * @throws HttpException
     * @throws \Throwable
     */
    public static function log($event, $params = null)
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        $le = new static();
        $le->user_fk = $contact->id;
        $le->event = $event;
        $le->params = (string)$params;
        if (!$le->save()) {
            throw new HttpException(200, "Ошибка LogEvent::log().", 200);
        }
    }
}