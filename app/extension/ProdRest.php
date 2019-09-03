<?php

namespace app\extension;


use app\modules\v1\models\pr\PrStorProd;

/**
 * Class ProdRest
 *
 * Класс с отчетами по остаткам продуктов
 * @package app\extension
 *
 */
class ProdRest
{

    /**
     * @var array - Матрица результатов
     */
    private $dataPrintPack = [];

    /**
     * @var array - Матрица результатов не учитывая упаковку
     */
    private $datePrint = [];

    /**
     * Массив с id продуктов
     * @param null $prodIds
     */
    function __construct($prodIds = null)
    {
        /** @var $storRest PrStorProd[] */
        $storRest = PrStorProd::readRest($prodIds);
        foreach ($storRest as $rest) {

            foreach (Sizes::fields as $fSize) {
                if ($rest->$fSize > 0) {

                    $blankId = $rest->blank_fk;
                    $printId = $rest->print_fk;
                    $packId = $rest->pack_fk;

                    if (!isset($this->dataPrintPack[$blankId][$printId][$packId][$fSize])) {
                        $this->dataPrintPack[$blankId][$printId][$packId][$fSize] = 0;
                    }
                    $this->dataPrintPack[$blankId][$printId][$packId][$fSize] = $rest->$fSize;

                    if (!isset($this->datePrint[$blankId][$printId][$fSize])) {
                        $this->datePrint[$blankId][$printId][$fSize] = 0;
                    }
                    $this->datePrint[$blankId][$printId][$fSize] += $rest->$fSize;

                }
            }
        }
    }

    /**
     * Вернуть остатки для указанного изделия без учета упаковки
     * @param $prodId
     * @param $printId
     * @param $fSize
     * @return int|mixed
     */
    public function getRestPrint($prodId, $printId, $fSize)
    {
        return (isset ($this->datePrint[$prodId][$printId][$fSize])) ?
            $this->datePrint[$prodId][$printId][$fSize] : 0;
    }

}