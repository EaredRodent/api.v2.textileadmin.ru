<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 11/14/2019
 * Time: 6:24 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\log\LogEvent;

class LogEventController extends ActiveControllerExtended
{
    public $modelClass = '';


    const actionGetEvents = 'GET /v1/log-event/get-events';

    /**
     * Вернуть все события для контактного лица в B2B кабинете,
     * если не указан contactID, то вернуть все события в B2B кабинете
     * @param int $contactID
     * @return mixed
     */
    function actionGetEvents($contactID = 0)
    {
        $events = [];

        if ($contactID) {
            $events = LogEvent::find()
                ->where(['user_fk' => $contactID])
                ->orderBy('ts_create DESC')
                ->all();
        } else {
            /** @var AnxUser[] $contacts */
            $contacts = AnxUser::find()
                ->where(['project' => 'b2b'])
                ->all();

            $contactIDs = [];

            foreach ($contacts as $contact) {
                $contactIDs[] = $contact->id;
            }

            $events = LogEvent::find()
                ->where(['user_fk' => $contactIDs])
                ->orderBy('ts_create DESC')
                ->all();
        }

        return $events;
    }
}