<?php


namespace app\objects;


use app\extension\Sizes;
use app\modules\v1\models\pr\PrStorProd;

/**
 * Предоставить отчеты по движению  продуктов на складе за определенный период
 */
class ProdMoveReport
{

    /**
     * @var Prices
     */
    private $prices;

    function __construct()
    {
        $this->prices = new Prices();
    }

    /**
     * Рассчитать стоимость товара, доставленного на склад за определенный период
     */
    public function getIncomProdCostCount($start, $end)
    {
        $cost = 0;
        $count = 0;

        //$log = [];
        //$prices = new Prices();

        /** @var $items PrStorProd[]*/
        $items = PrStorProd::readRecs(['in-production'], $start, $end);
        foreach ($items as $item) {
            foreach (Sizes::fields as $fSize) {
                if ($item->$fSize > 0) {
                    $count += $item->$fSize;
                    $price = $this->prices->getPrice($item->blank_fk, $item->print_fk, $fSize);
                    $cost += $item->$fSize * $price;
                    //$log[] = "{$item->blank_fk}, {$item->print_fk}, $fSize, {$item->$fSize}, {$price}";
                }
            }
        }

        //return ['count' => $count, 'cost' => $cost, 'log' => $log];
        return ['count' => $count, 'cost' => $cost];

    }
}