<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;

use app\gii\GiiRefBlankModel;

class RefBlankModel extends GiiRefBlankModel
{
    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'sexFk',
            'classFk'
        ]);
    }

}