<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 28.05.2019
 * Time: 13:51
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\ref\RefFabricType;

class RefFabricTypeController extends ActiveControllerExtended
{
    /** @var RefFabricType $modelClass */
	public $modelClass = 'app\modules\v1\models\ref\RefFabricType';
}