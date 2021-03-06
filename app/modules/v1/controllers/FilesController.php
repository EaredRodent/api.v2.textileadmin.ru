<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\classes\ExcelDescriptOrder;
use app\modules\v1\classes\ExcelInvoicesOrder;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\classes\ExcelPrice2;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsOrder;
use app\modules\v1\models\sls\SlsOrg;
use tests\unit\models\ContactFormTest;
use Yii;
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
     * @param $key
     * @param $dir
     * @param $name
     * @return string
     * @throws HttpException
     */
    public function actionGet($key, $dir, $name)
    {
        if ($key === AppMod::apiFilesGetPublicKey) {
            return $this->actionPublic($dir, $name);
        } else {
            throw new HttpException(403, 'Нет доступа');
        }

    }

    /**
     * Вернуть документы для контактных лиц Клиента b2b-кабинета
     * @param $dir
     * @param $urlKey
     * @param $id
     * @return void|\yii\console\Response|\yii\web\Response
     * @throws HttpException
     */
    public function actionGetOrderDoc($urlKey, $dir, $id)
    {
        $userByUrlKey = AnxUser::findOne(['url_key' => $urlKey]);

        if (!$userByUrlKey) {
            throw new HttpException(200, 'Внутренняя ошибка №1.', 200);
        }

        $orgFk = $userByUrlKey->org_fk;

        /** @var $order SlsOrder */
        $order = SlsOrder::get((int)$id);
        if (!$order) {
            throw new HttpException(200, 'Внутренняя ошибка №2.', 200);
        }
        $ownerOrgFk = $order->clientFk->org_fk;


        if ($orgFk === $ownerOrgFk) {
            if ($dir === 'export1c') {
                $obj = new ExcelInvoicesOrder($id, null);
                $obj->send();
                return;
            }

            if ($dir === 'description') {
                $obj = new ExcelDescriptOrder($id, null);
                $obj->send();
                return;
            }

            if ($dir === 'invoice') $path = Yii::getAlias(AppMod::pathDocInvoice) . "/invoice";
            if ($dir === 'waybill') $path = Yii::getAlias(AppMod::pathDocWaybill) . "/torg12";

            $name = "{$id}.pdf";
            $fullPath = $path . "-" . $name;

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
            throw new HttpException(403, 'Нет доступа');
        }
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * @param $urlKey
     * @param $fileName
     * @throws HttpException
     */
    function actionGetPrice($urlKey, $fileName)
    {
        $filePath = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_Prices]) . '/' . $urlKey . '/' . $fileName;

        if (!file_exists($filePath)) {
            throw new HttpException(200, 'Нет доступа.', 200);
        }

        $viewFileName = 'OXOUNO-price-' . date("d.m.y-H:i:s.", filectime($filePath)) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $viewFileName . ' ');
        header('Cache-Control: max-age=0');
        readfile($filePath);
        exit;
    }
}