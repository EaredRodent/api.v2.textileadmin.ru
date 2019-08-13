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
use yii\web\HttpException;


class FilesController extends ActiveControllerExtended
{
	public $modelClass = '';

	const actionGetInvoiceAttachment = 'GET /v1/files/get-invoice-attachment';

    /**
     * Вернуть в браузер файл прикрепленный к счету
     * @param $fileName
     * @return string
     */
	public function actionGetInvoiceAttachment($fileName)
	{

        $fullPath = \Yii::getAlias(AppMod::prmPathToSlsMailAttachments) . "/{$fileName}";

        if (!file_exists($fullPath)) {
            throw new HttpException(200, 'Файл не найден', 200);
        } else {
            $contentType = mime_content_type($fullPath);
            return \Yii::$app->response->sendFile($fullPath, $fileName, [
                'fileSize' => filesize($fullPath),
                'mimeType' => $contentType,
                'inline'   => true,
            ]);
        }
	}

}