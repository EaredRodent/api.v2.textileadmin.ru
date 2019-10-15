<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\services\ServReCAPTCHA;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use yii\web\HttpException;


class SlsClientController extends ActiveControllerExtended
{
    /** @var SlsClient $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsClient';

    const actionGetForFilters = 'GET /v1/sls-client/get-for-filters';

    /**
     * Вернуть список клиентов ссортировкой по short_name
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForFilters()
    {
        return SlsClient::find()
            ->orderBy('short_name')
            ->all();
    }

    const actionGetLegalEntitiesByOrgId = 'GET /v1/sls-client/get-legal-entities-by-org-id';

    /**
     * Возвращает юр.лиц для организации (b2b)
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionGetLegalEntitiesByOrgId($id)
    {
        return SlsClient::find()->where(['org_fk' => $id])->all();
    }

    const actionCreateLegalEntityForOrg = 'POST /v1/sls-client/create-legal-entity-for-org';

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     */
    function actionCreateLegalEntityForOrg($id)
    {
        $slsClient = new SlsClient();
        $slsClient->org_fk = $id;
        $slsClient->short_name = 'Новое юр.лицо';
        $slsClient->full_name = 'Новое юр.лицо';
        $slsClient->inn = random_int(100000, 999999) . random_int(100000, 999999);

        if (!$slsClient->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }

    const actionGetOutdatedLegalEntities = 'GET /v1/sls-client/get-outdated-legal-entities';

    /**
     * @return array
     */
    public function actionGetOutdatedLegalEntities()
    {
        return SlsClient::findAll(['org_fk' => null]);
    }

    const actionImportLegalEntity = 'POST /v1/sls-client/import-legal-entity';

    /**
     * Импортирует юр.лицо из старого TA в организацию $org_fk
     * @param $id
     * @param $org_fk
     * @return array
     * @throws HttpException
     */
    public function actionImportLegalEntity($id, $org_fk)
    {
        $slsClient = SlsClient::get($id);
        $slsClient->org_fk = $org_fk;

        if (!$slsClient->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }
}