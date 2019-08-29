<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use tests\unit\models\ContactFormTest;
use yii\web\Controller;
use yii\web\HttpException;


class FilesController extends Controller
{

    /**
     * Выдать файл находящийся в публичном доступе
     * Пример:
     * http://api.textileadmin.loc/v1/files/public/filesImageBaseProds/0048_1.jpg
     *
     * @param $dir
     * @param $name
     * @return \yii\console\Response|\yii\web\Response
     * @throws HttpException
     */
    public function actionPublic($dir, $name)
    {
        if (isset(AppMod::filesRout[$dir])) {
            $fullPath = \Yii::getAlias(AppMod::filesRout[$dir]) . "/{$name}";
            if (!file_exists($fullPath)) {
                throw new HttpException(200, 'Файл не найден', 200);
            } else {
                $contentType = mime_content_type($fullPath);
                return \Yii::$app->response->sendFile($fullPath, $name, [
                    'fileSize' => filesize($fullPath),
                    'mimeType' => $contentType,
                    'inline' => true,
                ]);
            }
        } else {
            throw new HttpException(404, 'Не найдена категория файла');
        }
    }

    /**
     * http://api.textileadmin.loc/v1/files/get?key=6spdsd4d44fsdaf89034&dir=filesInvoiceAttachement&name=1068-01082019molodcova.jpeg
     * http://api.textileadmin.loc/v1/files/get/6spdsd4d44fsdaf89034/filesInvoiceAttachement/1068-01082019molodcova.jpeg
     * Вернуть в браузер файл прикрепленный к счету
     * @param $name
     * @return string
     */
    public function actionGet($key, $dir, $name)
    {
        if ($key === "6spdsd4d44fsdaf89034") {
            return $this->actionPublic($dir, $name);
        } else {
            throw new HttpException(403, 'Нет доступа');
        }

    }

}