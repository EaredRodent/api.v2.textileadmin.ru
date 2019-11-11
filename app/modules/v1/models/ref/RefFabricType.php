<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;

use app\gii\GiiRefFabricType;

class RefFabricType extends GiiRefFabricType
{

    /**
     * Поля по уходу за тканью
     */
    const fCares = ['care1', 'care2', 'care3', 'care4', 'care5', 'care6'];

    /**
     * Выдать в массив значки по уходу за тканью
     */
    public function calcCare()
    {
        $resp = [];
        foreach (self::fCares as $fCare) {
            if ($this->$fCare) {
                $resp[] = $this->$fCare;
            }
        }
        return $resp;
    }

}