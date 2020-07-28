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

    const actionGetOutlookMeta = 'GET /v1/outlook/get-outlook-meta';

    /**
     * Outlook meta
     */
    function actionGetOutlookMeta() {
        $imgSetDir = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_OutlookImgSet]);
        $dirs = glob($imgSetDir . '/*', GLOB_ONLYDIR);
        $meta = [];

        foreach ($dirs as $dir) {
            $dirName = basename($dir);
            $dirPreviewImgDir = glob($dir . '/*.*')[0];
            $sizes = getimagesize($dirPreviewImgDir);
            $dirPreviewImg = pathinfo($dirPreviewImgDir)['basename'];
            $meta[] = [
                'name' => $dirName,
                'previewImg' => CURRENT_API_URL . '/outlook/' . $dirName . '/' . $dirPreviewImg,
                'width' => $sizes[0],
                'height' => $sizes[1]
            ];
        }

        return $meta;
    }

    const actionGetOutlook = 'GET /v1/outlook/get-outlook';

    /**
     * @param $archiveNumber
     * @return array
     */
    function actionGetOutlook($archiveNumber)
    {
        $dir = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_OutlookImgSet]) . '/' . $archiveNumber;
        $fileNames = glob($dir . '/*.*');
        $fileUrls = [];

        foreach ($fileNames as $fileName) {
            $fileUrls[] = CURRENT_API_URL . '/outlook/' . $archiveNumber . '/' . pathinfo($fileName)['basename'];
        }

        return $fileUrls;
    }

    const actionUploadOutlook = 'POST /v1/outlook/upload-outlook';

    /**
     * Upload outlook
     * @param $archiveNumber
     * @throws \yii\base\Exception
     */
    public function actionUploadOutlook($archiveNumber)
    {
        $imgSetDir = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_OutlookImgSet]) . '/' . $archiveNumber;

        if(file_exists($imgSetDir)) {
            $fileNames = glob($imgSetDir . '/*.*');
            foreach ($fileNames as $fileName) {
                unlink($fileName);
            }
            rmdir($imgSetDir);
        } else {
            mkdir($imgSetDir);
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