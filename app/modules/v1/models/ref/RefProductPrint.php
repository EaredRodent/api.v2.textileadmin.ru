<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankClass;
use app\gii\GiiRefProductPrint;

/**
 * Class RefBlankClass
 * @property RefBlankModel[] $refBlankModelsTree
 */
class RefProductPrint extends GiiRefProductPrint
{
    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'blankFk',
            'printFk'
        ]);
    }
}