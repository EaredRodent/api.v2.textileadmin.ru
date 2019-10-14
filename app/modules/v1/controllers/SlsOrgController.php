<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/11/2019
 * Time: 6:11 PM
 */

namespace app\modules\v1\controllers;


use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsOrg;
use yii\web\HttpException;

class SlsOrgController extends ActiveControllerExtended
{
    /** @var SlsOrg $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsOrg';

    const actionGetOrgs = 'GET /v1/sls-org/get-orgs';

    /**
     * Получает список всех организаций
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetOrgs()
    {
        return SlsOrg::find()->all();
    }

    const actionAccept = 'POST /v1/sls-org/accept';

    public function actionAccept($id)
    {
        $org = SlsOrg::findOne(['id' => $id]);
        if (!$org) {
            throw new HttpException(200, 'Такой организации не существует.', 200);
        }
        $org->state = 'accept';
        if (!$org->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        /** @var AnxUser[] $clients */
        $clients = AnxUser::findAll(['org_fk' => $org->id]);
        foreach ($clients as $client) {
            $client->status = 1;
            if(!$client->save()) {
                throw new HttpException(200, 'Ошибка при обновлении статуса клиента.', 200);
            }
        }
    }
}