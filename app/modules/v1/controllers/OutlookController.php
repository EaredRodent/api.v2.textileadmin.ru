<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 7/17/2020
 * Time: 11:56 PM
 */

namespace app\modules\v1\controllers;


use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use Yii;
use ZipArchive;

class OutlookController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetOutlook = 'GET /v1/outlook/get-outlook';

    /**
     * @return array
     */
    function actionGetOutlook()
    {
        $dir = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_OutlookImgSet]);
        $fileNames = glob($dir . '/*.*');
        $fileUrls = [];

        foreach ($fileNames as $fileName) {
            $fileUrls[] = CURRENT_API_URL . '/v1/files/public/filesB2B_OutlookImgSet/' . pathinfo($fileName)['basename'];
        }

        return $fileUrls;
    }

    const actionUploadOutlook = 'POST /v1/outlook/upload-outlook';

    /**
     * Upload outlook
     */
    public function actionUploadOutlook()
    {
        $imgSetDir = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_OutlookImgSet]);

        $fileNames = glob($imgSetDir . '/*.*');
        foreach ($fileNames as $fileName) {
            unlink($fileName);
        }

        $file = array_values($_FILES)[0];

        $zip = new ZipArchive();
        $res = $zip->open($file['tmp_name']);

        if ($res) {
            $zip->extractTo($imgSetDir);
            $zip->close();
        }

        $hash = Yii::$app->security->generateRandomString(8);
        $fileNames = glob($imgSetDir . '/*.*');
        foreach ($fileNames as $fileName) {
            $newFileName = pathinfo($fileName)['dirname'] . '/' . str_pad(pathinfo($fileName)['filename'], 4, '0', STR_PAD_LEFT) . '_' . $hash . '.' . pathinfo($fileName)['extension'];
            rename($fileName, $newFileName);
        }

        // Модель не обновлялась, но нужно уведомить о загруженном файле

        Yii::$app->getModule('v1')->cmdTables[] = 'outlook';
    }
}