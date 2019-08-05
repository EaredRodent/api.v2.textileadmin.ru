<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\models\AnxUser;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\BaseClassTemp;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsMoney;
use ReflectionClass;
use Yii;
use yii\web\HttpException;


class BaseController extends ActiveControllerExtended
{

    public $modelClass = '';

    const actionGetControllers = 'GET /v1/base/get-controllers';

    /**
     * Вернуть дерево контроллеры/экшены
     * @return array
     */
    public function actionGetControllers()
    {
        return BaseClassTemp::getApi2();
    }

    const actionPostTestData = 'POST /v1/base/post-test-data';

    /**
     * Тестовый экшен
     * @param $param1
     * @param $param2
     * @return array
     * @throws HttpException
     */
    public function actionPostTestData($param1, $param2)
    {

        if ($param1 <= 100) {
            return ['result' => 'zbs'];
        }

        if ($param1 > 100) {
            $strError = "Параметр \$param1 не божет быть > 100, а ты ввел $param1";
            throw new HttpException(406, $strError);

            // throw new H($strError);
        } else {
            return ['recs' => $kokoko];
        }
    }

}