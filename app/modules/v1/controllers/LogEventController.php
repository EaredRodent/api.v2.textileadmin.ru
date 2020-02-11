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
use Yii;

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
        $timeStart = date('Y-m-d H:i:s', time() - 24 * 60 * 60 * 3);

        if ($contactID) {
            $events = LogEvent::find()
                ->where(['user_fk' => $contactID])
                ->andWhere(['>', 'ts_create', $timeStart])
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
                ->andWhere(['>', 'ts_create', $timeStart])
                ->orderBy('ts_create DESC')
                ->all();
        }

        return $events;
    }

    const actionLogBrowser = 'GET /v1/log-event/log-browser';

    /**
     * Логирует старый браузер (B2B)
     */
    function actionLogBrowser()
    {
        $params = json_encode([
            'userIP' => Yii::$app->request->headers->get('x-forwarded-for'),
            'userAgent' => Yii::$app->request->userAgent
        ], JSON_UNESCAPED_UNICODE);

        $logEvent = LogEvent::findOne(['event' => 'LogOutdatedBrowser', 'params' => $params]);

        if(!$logEvent) {
            $logEvent = new LogEvent();
            $logEvent->event = 'LogOutdatedBrowser';
            $logEvent->params = $params;
            $logEvent->save();
        }
    }

    const actionGetOutdatedBrowsers = 'GET /v1/log-event/get-outdated-browsers';

    /**
     * Возвращает события лога старых браузеров
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionGetOutdatedBrowsers()
    {
        $events = LogEvent::find()
            ->where(['event' => 'LogOutdatedBrowser'])
            ->all();
        return $events;
    }
}