<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/19/2019
 * Time: 3:15 PM
 */

namespace app\modules\v1\models\sls;


use app\extension\Sizes;
use app\gii\GiiSlsItem;
use app\modules\v1\classes\CardProd;
use app\modules\v1\models\ref\RefProductPrint;

class SlsItem extends GiiSlsItem
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'cardProd' => function () {
                $prod = $this->blankFk;
                $postProd = null;

                if($this->print_fk !== 1) {
                    $postProd = RefProductPrint::find()
                        ->where(['blank_fk' => $this->blank_fk])
                        ->andWhere(['print_fk' => $this->print_fk])
                        ->one();
                }
                return new CardProd($postProd ? $postProd : $prod);
            }
        ]);
    }
}