<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;

use app\gii\GiiRefBlankTheme;
use app\modules\AppMod;

class RefBlankTheme extends GiiRefBlankTheme
{
    public function hArt()
    {
        return str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function hThemeDescript()
    {
        if ($this->descript) {
            return "{$this->title} ({$this->descript})";
        } else {
            return "{$this->title}";
        }
    }

    public function hGetPhotoAddr()
    {
        $fullPath = \Yii::getAlias(AppMod::filesRout[AppMod::filesImageThemes]) . "/theme_{$this->hArt()}.jpg";
        if (file_exists($fullPath)) {
            return CURRENT_API_URL . '/v1/files/public/' . AppMod::filesImageThemes . "/theme_{$this->hArt()}.jpg";
        } else {
            return null;
        }
    }
}