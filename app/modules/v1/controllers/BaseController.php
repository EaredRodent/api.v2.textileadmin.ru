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
use ReflectionClass;
use Yii;


class BaseController extends ActiveControllerExtended
{

    public $modelClass = '';

    const actionGetControllers = 'GET /v1/base/get-controllers';

    public function actionGetControllers()
    {
        $path = Yii::getAlias('@app/modules/v1/controllers/*.php');
        $files = glob($path);


        $listControllers = [];
        foreach ($files as $file) {
            $listControllers[] = basename($file);
        }

        $className = 'app\modules\v1\controllers\SlsInvoiceController';
        $m = get_class_methods($className);
        // $m = get_class_vars($className);

        $refl = new ReflectionClass($className);
        $list = $refl->getConstants();

        return $list;
    }

}