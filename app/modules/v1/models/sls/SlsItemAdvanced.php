<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/21/2019
 * Time: 6:08 PM
 */

namespace app\modules\v1\models\sls;


use app\extension\Sizes;

class SlsItemAdvanced extends SlsItem
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'includes' => function () {
                $result = [];
                $sexType = $this->blankFk->calcSizeType();

                foreach (Sizes::prices as $size => $price) {
                    if ($this->$size) {
                        $result[] = ['size' => $size,'sizeStr' => Sizes::typeCompare[$sexType][$size], 'count' => $this->$size, 'price' => $this->$price];
                    }
                }
                return $result;
            },
            'count' => function () {
                $count = 0;
                foreach (Sizes::fields as $size) {
                    if ($this->$size) {
                        $count += $this->$size;
                    }
                }
                return $count;
            },
            'sum' => function () {
                $sum = 0;
                foreach (Sizes::prices as $size => $price) {
                    if ($this->$size) {
                        $sum += $this->$size * $this->$price;
                    }
                }
                return $sum;
            }
        ]);
    }
}