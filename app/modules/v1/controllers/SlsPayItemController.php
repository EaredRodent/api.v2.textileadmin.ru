<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10.06.2019
 * Time: 12:56
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsPayItem;

class SlsPayItemController extends ActiveControllerExtended
{
    /** @var SlsPayItem $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsPayItem';

    const getGetOut = 'GET /v1/sls-pay-item/get-out';

    public function actionGetOut()
    {
        return SlsPayItem::getOut();
    }

    const getGetIn = 'GET /v1/sls-pay-item/get-in';

    public function actionGetIn()
    {
        return SlsPayItem::getIn();
    }
}
