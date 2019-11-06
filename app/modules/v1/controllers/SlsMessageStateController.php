<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/6/2019
 * Time: 2:29 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsMessage;
use app\modules\v1\models\sls\SlsMessageState;
use app\modules\v1\models\sls\SlsOrg;
use Yii;

class SlsMessageStateController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetForContact = 'GET /v1/sls-message-state/get-for-contact';

    /**
     * Возвращает информацию о сообщениях организации для контактного лица
     * @return array
     * unreadCount - кол-во непрочитанных сообщений
     * @throws \Throwable
     */
    public function actionGetForContact()
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsMessageState $messageState */
        $messageState = SlsMessageState::find()
            ->where(['user_fk' => $contact->id])
            ->andWhere(['org_fk' => $contact->org_fk])
            ->one();

        $last_message_fk = 0;

        if ($messageState && $messageState->last_message_fk) {
            $last_message_fk = $messageState->last_message_fk;
        }
        $unreadMessages = SlsMessage::find()
            ->where(['org_fk' => $contact->org_fk])
            ->andWhere(['>', 'id', $last_message_fk])
            ->all();
        $unreadCount = count($unreadMessages);

        return [
            'unreadCount' => $unreadCount
        ];
    }

    const actionGetForManager = 'GET /v1/sls-message-state/get-for-manager';

    /**
     * Возвращает информацию о сообщениях всех организаций для менеджера
     * @return array
     * unreadCount - кол-во непрочитанных сообщений
     * @throws \Throwable
     */
    public function actionGetForManager()
    {
        $result = [];

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        /** @var SlsOrg[] $orgs */
        $orgs = SlsOrg::find()->all();

        foreach ($orgs as $org) {
            /** @var SlsMessageState $messageState */
            $messageState = SlsMessageState::find()
                ->where(['user_fk' => $contact->id])
                ->andWhere(['org_fk' => $org->id])
                ->one();

            $last_message_fk = 0;

            if ($messageState && $messageState->last_message_fk) {
                $last_message_fk = $messageState->last_message_fk;
            }

            $unreadMessages = SlsMessage::find()
                ->where(['org_fk' => $org->id])
                ->andWhere(['>', 'id', $last_message_fk])
                ->all();
            $unreadCount = count($unreadMessages);

            $result[$org->id] = [
                'unreadCount' => $unreadCount
            ];
        }

        return $result;
    }
}