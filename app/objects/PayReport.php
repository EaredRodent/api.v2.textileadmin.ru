<?php


namespace app\objects;


use app\extension\Sizes;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProductPrint;
use Yii;

/**
 * Предоставить инфу по ценам
 * Тестирование /v1/test/obj-prices
 */
class PayReport
{

    public $queryResult;

    public $matrix = [];
    public $axisX;
    public $axisY;
    public $totalX = [];
    public $totalY = [];
    public $totalCommon = 0;


    // Дополнительное значение (к основному при необходимости - деньги или штуки)
    public $matrixAdd = [];
    public $totalXAdd = [];
    public $totalYAdd = [];
    public $totalCommonAdd = 0;


    // Результирующая матрица значений
    public $matrixResult = [];

    private $matrixCollects = [];


    function __construct($dateStart, $dateEnd,
                         $articles, $sex, $groups, $fabrics, $tags, $clients, $managers, $types,
                         $axisX, $axisY, $resultType, $sortType)
    {

        $prods = RefArtBlank::getAll();
        foreach ($prods as $prod) {
            $this->matrixCollects[$prod->id][1] = $prod->collection_fk;
        }
        $prodsPost = RefProductPrint::getAll();
        foreach ($prodsPost as $prodPost) {
            $this->matrixCollects[$prodPost->blank_fk][$prodPost->print_fk] = $prodPost->collection_fk;
        }

        // Формирование выборки по запросу
        $havingSql = "HAVING ";
        $arrayHaving = [];

        if ($sex) {
            $arrSex = [];
            foreach ($sex as $sexItem) $arrSex[] = "'{$sexItem}'";
            $sexStr = implode(', ', $arrSex);
            $arrayHaving[] = "sexStr IN ({$sexStr})";
        }

        if ($groups) {
            $arrGroup = [];
            foreach ($groups as $group) $arrGroup[] = "'{$group}'";
            $gropuStr = implode(', ', $arrGroup);
            $arrayHaving[] = "groupStr IN ({$gropuStr})";
        }

        if ($fabrics) {
            $arrFabrics = [];
            foreach ($fabrics as $fabric) $arrFabrics[] = "'{$fabric}'";
            $fabricStr = implode(', ', $arrFabrics);
            $arrayHaving[] = "fabricStr IN ({$fabricStr})";
        }

        if ($clients) {
            $arrClients = [];
            foreach ($clients as $client) $arrClients[] = "'{$client}'";
            $clientsStr = implode(', ', $arrClients);
            $arrayHaving[] = "clientName IN ({$clientsStr})";
        }

        if ($tags) {
            $arrTags = [];
            foreach ($tags as $tag) $arrTags[] = "'{$tag}'";
            $tagsStr = implode(', ', $arrTags);
            $arrayHaving[] = "tag IN ({$tagsStr})";
        }

        if ($managers) {
            $arrManagers = [];
            foreach ($managers as $manager) $arrManagers[] = "'{$manager}'";
            $managersStr = implode(', ', $arrManagers);
            $arrayHaving[] = "managerName IN ({$managersStr})";
        }

        if ($articles) {
            $arrArts = [];
            foreach ($articles as $art) $arrArts[] = "'{$art}'";
            $artsStr = implode(', ', $arrArts);
            $arrayHaving[] = "art IN ({$artsStr})";
        }

        if (empty($arrayHaving)) {
            $havingSql = '';
        } else {
            $havingSql .= implode(' AND ', $arrayHaving);
        }


        $sql = "
        SELECT 
            sls_item.id,
            sls_order.ts_send,
            YEAR(sls_order.ts_send) AS year,
            CONCAT(YEAR(sls_order.ts_send), '-', MONTH(sls_order.ts_send)) AS month,
            sls_order.user_fk,
            anx_user.name AS managerName,
            sls_order.client_fk, 
            sls_client.short_name AS clientName,
            order_fk,
            ref_blank_model.sex_fk AS sexId,
            ref_blank_sex.code_ru AS sexStr,
            ref_blank_class.tag AS tag,
            ref_blank_group.title AS groupStr,
            CONCAT ('OXO-', LPAD(blank_fk, 4, '0'), IF(print_fk > 3, CONCAT('-', LPAD(print_fk, 3, '0')), '')) AS art,
               
            ref_fabric_type.type_price AS fabricStr,
               
            blank_fk,
            print_fk,
        
            # size_5xs,	size_4xs,	size_3xs,	size_2xs,	size_xs, size_s, size_m, size_l, size_xl,	size_2xl,	size_3xl,	size_4xl, 

            ( IFNULL(size_5xs, 0) 
            + IFNULL(size_4xs, 0) 
            + IFNULL(size_3xs, 0) 
            + IFNULL(size_2xs, 0) 
            + IFNULL(size_xs,  0) 
            + IFNULL(size_s,   0) 
            + IFNULL(size_m,   0) 
            + IFNULL(size_l,   0) 
            + IFNULL(size_xl,  0) 
            + IFNULL(size_2xl, 0) 
            + IFNULL(size_3xl, 0) 
            + IFNULL(size_4xl, 0)) AS rowCount,

            ( IFNULL(size_5xs * sls_item.price_5xs, 0) 
            + IFNULL(size_4xs * sls_item.price_4xs, 0) 
            + IFNULL(size_3xs * sls_item.price_3xs, 0) 
            + IFNULL(size_2xs * sls_item.price_2xs, 0) 
            + IFNULL(size_xs  * sls_item.price_xs,  0) 
            + IFNULL(size_s   * sls_item.price_s,   0) 
            + IFNULL(size_m   * sls_item.price_m,   0) 
            + IFNULL(size_l   * sls_item.price_l,   0) 
            + IFNULL(size_xl  * sls_item.price_xl,  0) 
            + IFNULL(size_2xl * sls_item.price_2xl, 0) 
            + IFNULL(size_3xl * sls_item.price_3xl, 0) 
            + IFNULL(size_4xl * sls_item.price_4xl, 0)) AS rowMoney
            
            FROM textile.sls_item
            
            LEFT JOIN sls_order ON sls_order.id = sls_item.order_fk
            LEFT JOIN anx_user ON anx_user.id = sls_order.user_fk
            LEFT JOIN sls_client ON sls_client.id = sls_order.client_fk
            LEFT JOIN ref_art_blank ON ref_art_blank.id = sls_item.blank_fk
            LEFT JOIN ref_fabric_type ON ref_fabric_type.id = ref_art_blank.fabric_type_fk
            LEFT JOIN ref_blank_model ON ref_blank_model.id = ref_art_blank.model_fk
            LEFT JOIN ref_blank_sex ON ref_blank_sex.id = ref_blank_model.sex_fk
            LEFT JOIN ref_blank_class ON ref_blank_class.id = ref_blank_model.class_fk
            LEFT JOIN ref_blank_group ON ref_blank_group.id = ref_blank_class.group_fk
            
            WHERE sls_order.status = 's7_send'
            AND sls_order.flag_return = 0
            AND sls_order.ts_send > '{$dateStart} 00:00:00'
            AND sls_order.ts_send < '{$dateEnd} 23:59:59'        
        " . $havingSql;

        $recs = Yii::$app->db->createCommand($sql)->queryAll();


        // Если есть фильтр - отфитровать по типу "ассорт матрица"||"акционный товар"
        if (!empty($types)) {
            foreach ($recs as $rec) {
                $recCollect = $this->getCollectId($rec['blank_fk'], $rec['print_fk']);
                $recCollectStr = ($recCollect > 0) ? 'matrix' : 'discount';
                if (in_array($recCollectStr, $types)) {
                    $this->queryResult[] = $rec;
                }
            }
        } else {
            $this->queryResult = $recs;
        }


        // Формирование вспомогательных переменных и отчета
        $this->axisX = $this->getUnicValues($axisX);
        $this->axisY = $this->getUnicValues($axisY);

        foreach ($this->axisX as $ax) {
            $this->totalX[$ax] = 0;
            $this->totalXAdd[$ax] = 0;
        }
        foreach ($this->axisY as $ay) {
            $this->totalY[$ay] = 0;
            $this->totalYAdd[$ay] = 0;
        }


        // Вместе с запросом может быть дополнительный параметр (+деньги или +штуки)
        $addResultType = null;

        // todo быдлокод
        if ($resultType === 'rowMoney') {
            $addResultType = 'rowCount';
        }
        if ($resultType === 'rowCount') {
            $addResultType = 'rowMoney';
        }
        if ($resultType === 'rowMoneyAddCount') {
            $resultType = 'rowMoney';
            $addResultType = 'rowCount';
        }
        if ($resultType === 'rowCountAddMoney') {
            $resultType = 'rowCount';
            $addResultType = 'rowMoney';
        }

        foreach ($this->queryResult as $rec) {
            $x = $rec[$axisX];
            $y = $rec[$axisY];
            $value = $rec[$resultType];

            if (!isset($this->matrix[$x][$y])) {
                $this->matrix[$x][$y] = 0;
            }
            $this->matrix[$x][$y] += $value;

            $this->totalX[$x] += $value;
            $this->totalY[$y] += $value;
            $this->totalCommon += $value;

            // Вычисление доп. значения при необходимости
            if ($addResultType) {
                $valueAdd = $rec[$addResultType];
                if (!isset($this->matrixAdd[$x][$y])) {
                    $this->matrixAdd[$x][$y] = 0;
                }
                $this->matrixAdd[$x][$y] += $valueAdd;
                $this->totalXAdd[$x] += $valueAdd;
                $this->totalYAdd[$y] += $valueAdd;
                $this->totalCommonAdd+= $valueAdd;
            }
        }

        foreach ($this->axisX as $ax) {
            foreach ($this->axisY as $ay) {
                $val = (isset($this->matrix[$ax][$ay])) ? $this->matrix[$ax][$ay] : 0;
                $valAdd = (isset($this->matrixAdd[$ax][$ay])) ? $this->matrixAdd[$ax][$ay] : 0;
                $this->matrixResult[] =[
                    'x' => $ax,
                    'y' => $ay,
                    'val' => $val,
                    'valAdd' => $valAdd
                ];
            }
        }

        // Сортировка по значению
        if ($sortType === 'valASK' || $sortType === 'valDESC') {
            $newArr = $this->totalX;
            natsort($newArr);
            if ($sortType === 'valDESC') {
                $newArr = array_reverse($newArr);
            }
            $newXArr = [];
            foreach ($newArr as $key => $val) {
                $newXArr[] = $key;
            }
            $this->axisX = $newXArr;
        }

    }


    /**
     * Вернуть уникальные значения по именованиям
     */
    public function getUnicValues($fName)
    {
        $arr = [];
        foreach ($this->queryResult as $rec) {
            if (!in_array($rec[$fName], $arr)) {
                $arr[] = $rec[$fName];
            }
        }
        sort($arr, SORT_NATURAL);
        return $arr;
    }

    private function getCollectId($blankId, $printId) {
        if (isset($this->matrixCollects[$blankId][$printId])) {
            return $this->matrixCollects[$blankId][$printId];
        } else {
            return null;
        }
    }

}