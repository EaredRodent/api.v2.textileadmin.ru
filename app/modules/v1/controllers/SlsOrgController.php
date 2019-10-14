<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/11/2019
 * Time: 6:11 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsOrg;

class SlsOrgController extends ActiveControllerExtended
{
    /** @var SlsOrg $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsOrg';

    const orgs = 'GET /v1/sls-org/orgs';

    public function orgs()
    {
        return SlsOrg::find()
            ->where(['project' => 'b2b'])
            ->all();
    }
}