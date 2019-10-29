<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/11/2019
 * Time: 3:00 PM
 */

namespace app\modules\v1\models\sls;

use app\gii\GiiSlsOrg;
use app\modules\AppMod;

class SlsOrg extends GiiSlsOrg
{
    const clientStatus = [
        0 => 'Базовый',
        1 => 'Розница ☆',
        2 => 'Розница ☆ ☆',
        3 => 'Розница ☆ ☆ ☆',
        4 => 'Опт Silver',
        5 => 'Опт Gold',
        6 => 'Опт Platinum',
    ];

    /**
     * @return array|false
     */
    public function fields()
    {
        return array_merge(parent::fields(), [

            'statusStr' => function () {
                return self::clientStatus[$this->status];
            },
            'managerFk',
        ]);
    }


}