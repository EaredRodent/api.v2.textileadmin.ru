<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/17/2019
 * Time: 12:16 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsMessage;
use Yii;
use yii\web\HttpException;

class SlsMessageController extends ActiveControllerExtended
{
    public $modelClass = 'app\modules\v1\models\sls\SlsMessage';

    const actionGetMessagesForClient = 'GET /v1/sls-message/get-messages-for-client';

    /**
     * Возвращает сообщения для клиента B2B
     * @return SlsMessage[]
     * @throws \Throwable
     */
    function actionGetMessagesForClient()
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();
        return SlsMessage::findAll(['org_fk' => $contact->org_fk]);
    }

    const actionSendFromClient = 'POST /v1/sls-message/send-from-client';

    /**
     * Сохраняет сообщение отпправленное клиентом из B2B кабинета
     * @param $message
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    function actionSendFromClient($message)
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        $msg = new SlsMessage();
        $msg->org_fk = $contact->org_fk;
        $msg->user_fk = $contact->id;
        $msg->message = $message;

        if (!$msg->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}