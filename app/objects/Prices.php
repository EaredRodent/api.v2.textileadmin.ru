<?php


namespace app\objects;


use app\extension\Sizes;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\ref\RefProductPrint;

/**
 * Предоставить инфу по ценам
 * Тестирование /v1/test/obj-prices
 */
class Prices
{
    // Матрица [артикул][принт][размер] = цена
    private $matrix;

    function __construct()
    {
        // Без принта
        /** @var $recs RefArtBlank[] */
        $recs = RefArtBlank::find()->all();
        foreach ($recs as $rec) {
            foreach (Sizes::prices as $fSize => $fPrice) {
                if ($rec->$fPrice > 0) {
                    $this->matrix[$rec->id][1][$fSize] = $rec->$fPrice;
                }
            }
        }

        // С принтом
        /** @var $recsPrint RefProductPrint[] */
        $recsPrint = RefProductPrint::find()->all();
        foreach ($recsPrint as $recPrint) {
            foreach (Sizes::prices as $fSize => $fPrice) {
                if ($recPrint->$fPrice > 0) {
                    $this->matrix[$recPrint->blank_fk][$recPrint->print_fk][$fSize] = $recPrint->$fPrice;
                }
            }
        }
    }

    /**
     * Вернуть цену по продукт/принт/размер
     * @param $prodId
     * @param $printId
     * @param $size
     * @return int
     * @throws \Exception
     */
    public function getPrice($prodId, $printId, $size)
    {
        $fSize = Sizes::getFieldSize($size);
        return (isset($this->matrix[$prodId][$printId][$fSize])) ?
            $this->matrix[$prodId][$printId][$fSize] : 0;
    }

}