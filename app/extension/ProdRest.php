<?php

namespace app\extension;


use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\sls\SlsItem;

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
     * @var array - Матрица [$blankId][$printId][$packId][$fSize] - на складе
     */
    private $matrixRest = [];

    /**
     * @var array - Матрица [$blankId][$printId][$packId][$fSize] - доуступно для заказа
     */
    private $matrixAvail = [];

    /**
     * @var array - Матрица результатов не учитывая упаковку
     */
    private $matrixWithoutPack = [];

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
                    //if (!isset($this->prodPrintPack[$blankId][$printId][$packId][$fSize])) {
                    //    $this->prodPrintPack[$blankId][$printId][$packId][$fSize] = 0;
                    //}
                    $this->matrixRest[$blankId][$printId][$packId][$fSize] = $rest->$fSize;
                    $this->matrixAvail[$blankId][$printId][$packId][$fSize] = $rest->$fSize;

//                    if (!isset($this->prodPrint[$blankId][$printId][$fSize])) {
//                        $this->prodPrint[$blankId][$printId][$fSize] = 0;
//                    }
//                    $this->matrixWithoutPack[$blankId][$printId][$fSize] = $rest->$fSize;

                }
            }
        }

        $reserves = SlsItem::readReserv($prodIds);
        foreach ($reserves as $reserve) {
            foreach (Sizes::fields as $fSize) {
                if ($reserve->$fSize > 0) {
                    $blankId = $reserve->blank_fk;
                    $printId = $reserve->print_fk;
                    $packId = $reserve->pack_fk;

                    if (!isset($this->matrixAvail[$blankId][$printId][$packId][$fSize])) {
                        $this->matrixAvail[$blankId][$printId][$packId][$fSize] = 0;
                    }
                    $this->matrixAvail[$blankId][$printId][$packId][$fSize] -= $reserve->$fSize;

//                    if (!isset($this->matrixWithoutPack[$blankId][$printId][$fSize])) {
//                        $this->matrixWithoutPack[$blankId][$printId][$fSize] = 0;
//                    }
//                    $this->matrixWithoutPack[$blankId][$printId][$fSize] -= $reserve->$fSize;

                }
            }
        }

    }

    /**
     * Вернуть остатки на складе
     * @param $prodId
     * @param $printId
     * @param $packId (1 - без кпаковки)
     * @param $size
     * @return int|mixed
     * @throws \Exception
     */
    public function getRest($prodId, $printId, $packId, $size)
    {
        $fSize = Sizes::getFieldSize($size);
        return (isset ($this->matrixRest[$prodId][$printId][$packId][$fSize])) ?
            $this->matrixRest[$prodId][$printId][$packId][$fSize] : 0;
    }

    /**
     * Вернуть кол-во достпуное для заказа
     * @param $prodId
     * @param $printId
     * @param $packId (1 - без кпаковки)
     * @param $size
     * @return int|mixed
     * @throws \Exception
     */
    public function getAvailForOrder($prodId, $printId, $packId, $size)
    {
        $fSize = Sizes::getFieldSize($size);
        return (isset ($this->matrixAvail[$prodId][$printId][$packId][$fSize])) ?
            $this->matrixAvail[$prodId][$printId][$packId][$fSize] : 0;
    }

}