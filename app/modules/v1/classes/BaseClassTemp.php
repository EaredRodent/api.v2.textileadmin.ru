<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 30.05.2019
 * Time: 12:50
 */

namespace app\modules\v1\classes;

use app\modules\v1\V1Mod;
use ReflectionClass;
use Yii;
use yii\db\ActiveRecord;


class BaseClassTemp
{
    static function getApi()
    {
        $nameSpace = 'app\modules\v1\controllers\\';

        $path = Yii::getAlias('@app/modules/v1/controllers/*.php');
        $files = glob($path);


        $listApi = [];

        foreach ($files as $file) {
            $controllerName = str_replace('.php', '', basename($file));
            $className = $nameSpace . $controllerName;
            $refl = new ReflectionClass($className);

            $listCnt = $refl->getConstants();
            unset($listCnt['EVENT_BEFORE_ACTION']);
            unset($listCnt['EVENT_AFTER_ACTION']);
            foreach ($listCnt as $key => $val) {


                $comment = ($refl->hasMethod($key)) ? $refl->getMethod($key)->getDocComment() : '';
                $listApi[$controllerName]['api'][] = [
                    'action' => $key,
                    'url' => $val,
                    'comment' => $comment,

                ];
            }


        }

        //$className = 'SlsInvoiceController';
        //$m = get_class_methods($className);
        // $m = get_class_vars($className);


        return $listApi;
    }

    static function getApi2()
    {
        $nameSpace = 'app\modules\v1\controllers\\';

        $path = Yii::getAlias('@app/modules/v1/controllers/*.php');
        $files = glob($path);


        $listApi = [];

        foreach ($files as $file) {
            $controllerName = str_replace('.php', '', basename($file));
            $className = $nameSpace . $controllerName;
            $refl = new ReflectionClass($className);

            $listCnt = $refl->getConstants();
            unset($listCnt['EVENT_BEFORE_ACTION']);
            unset($listCnt['EVENT_AFTER_ACTION']);

            $curObj = [
                'name' => $controllerName,
                'key' => $controllerName,
                'children' => [],
            ];

            foreach ($listCnt as $key => $val) {
                $comment = ($refl->hasMethod($key)) ? $refl->getMethod($key)->getDocComment() : '';
                $curObj['children'][] = [
                    'name' => $key,
                    'key' => $controllerName . $key,
                    'url' => $val,
                    'comment' => $comment,
                ];
            }

            $listApi[] = $curObj;

        }

        return $listApi;
    }
}