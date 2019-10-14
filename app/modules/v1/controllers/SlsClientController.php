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
}