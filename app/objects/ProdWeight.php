<?php


namespace app\objects;


use app\extension\Sizes;
use app\modules\v1\models\ref\RefWeight;

/**
 * Верунть вес изделия
 * Тестирование /v1/test/obj-prices
 */
class ProdWeight
{
    // Матрица [id модели][тип ткани][размер] = вес
    private $matrix;

    function __construct()
    {
        // Без принта
        /** @var $recs RefWeight[] */
        $recs = RefWeight::find()->all();
        foreach ($recs as $rec) {
            foreach (Sizes::prices as $fSize => $fPrice) {
                if ($rec->$fSize > 0) {
                    $this->matrix[$rec->model_fk][$rec->fabric_fk][$fSize] = $rec->$fSize;
                }
            }
        }
    }

    /**
     * Вернуть вес для модели/ткани/размера
     * @param $modelId
     * @param $fabricId
     * @param $size
     * @return int
     * @throws \Exception
     */
    public function getWeight($modelId, $fabricId, $size)
    {
        $fSize = Sizes::getFieldSize($size);
        return (isset($this->matrix[$modelId][$fabricId][$fSize])) ?
            $this->matrix[$modelId][$fabricId][$fSize] : 0;
    }

}