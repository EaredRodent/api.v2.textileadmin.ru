<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;

use app\gii\GiiRefBlankTheme;

class RefBlankTheme extends GiiRefBlankTheme
{
    public function hArt()
    {
        return str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}