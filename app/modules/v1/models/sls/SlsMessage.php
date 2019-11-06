<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/17/2019
 * Time: 12:15 PM
 */

namespace app\modules\v1\models\sls;


use app\gii\GiiSlsMessage;
use app\models\AnxUser;
use Yii;
use yii\web\HttpException;

class SlsMessage extends GiiSlsMessage
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'userFk'
        ]);
    }

    static public function getMessages($org_fk)
    {
        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();
        /** @var SlsMessage[] $messages */
        $messages = SlsMessage::findAll(['org_fk' => $org_fk]);

        if (count($messages)) {
            $messageState = SlsMessageState::find()
                ->where(['user_fk' => $contact->id])
                ->andWhere(['org_fk' => $org_fk])
                ->one();

            if (!$messageState) {
                $messageState = new SlsMessageState();
                $messageState->user_fk = $contact->id;
                $messageState->org_fk = $org_fk;
            }

            $messageState->last_message_fk = $messages[count($messages) - 1]->id;

            if (!$messageState->save()) {
                throw new HttpException(200, 'Внутренняя ошибка.', 200);
            }
        }

        return $messages;
    }
}