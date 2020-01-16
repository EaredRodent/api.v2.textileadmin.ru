<?php


namespace app\objects;


use app\extension\Sizes;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\ref\RefProductPrint;

/**
 * Предоставить инфу по ценам
 * И о том, включен ли в прайс или нет
 * Тестирование /v1/test/obj-prices
 */
class Prices
{
    // Матрица [артикул][принт][размер] = цена
    private $matrix;

    // Матрица [артикул][принт] = flagInPrice
    private $matrixInPrice;

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
            $this->matrixInPrice[$rec->id][1] = (bool) $rec->flag_price;
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
            $this->matrixInPrice[$recPrint->blank_fk][$recPrint->print_fk] = (bool) $recPrint->flag_price;
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

    /**
     * Вернуть минимальную базовую цену для продукт/принт
     * Есть максимальная для больших размеров
     * @param $prodId
     * @param $printId
     * @param $size
     * @return int
     * @throws \Exception
     */
    public function getMinPrice($prodId, $printId)
    {
        foreach (Sizes::fields as $fSize) {
            if (isset($this->matrix[$prodId][$printId][$fSize])) {
                return $this->matrix[$prodId][$printId][$fSize];
            }
        }
        return null;
    }

    /**
     * Вернуть flagInPrice
     * @param $prodId
     * @param $printId
     * @return bool
     * @throws \Exception
     */
    public function getFlagInPrice($prodId, $printId)
    {
        return (isset($this->matrixInPrice[$prodId][$printId])) ?
            $this->matrixInPrice[$prodId][$printId] : false;
    }

}