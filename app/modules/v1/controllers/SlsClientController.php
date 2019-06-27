<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;


class SlsClientController extends ActiveControllerExtended
{
    /** @var SlsClient $modelClass */
	public $modelClass = 'app\modules\v1\models\sls\SlsClient';

    /**
     * Вернуть список клиентов ссортировкой по short_name
     */

	const getGetForFilters = 'GET /v1/sls-client/get-for-filters';

	public function actionGetForFilters()
	{
		return SlsClient::find()
			->orderBy('short_name')
			->all();
	}

}