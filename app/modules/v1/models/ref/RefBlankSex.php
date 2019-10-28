<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefBlankClass;
use app\gii\GiiRefBlankSex;

class RefBlankSex extends GiiRefBlankSex
{


    /**
     * Преобразует массив упрощенных тегов пола в строки title из таблицы ref_blank_sex
     * @param $sexTags
     * @return array
     */
    public static function calcSexTagsToRealTitles($sexTags)
    {
        $sexTitles = [];

        if (in_array('Женщинам', $sexTags)) {
            $sexTitles = array_merge($sexTitles, ['Женский', 'Унисекс взрослый']);
        }

        if (in_array('Мужчинам', $sexTags)) {
            $sexTitles = array_merge($sexTitles, ['Мужской', 'Унисекс взрослый']);
        }

        if (in_array('Девочкам', $sexTags)) {
            $sexTitles = array_merge($sexTitles, ['Для девочек', 'Унисекс детский']);
        }

        if (in_array('Мальчикам', $sexTags)) {
            $sexTitles = array_merge($sexTitles, ['Для мальчиков', 'Унисекс детский']);
        }

        return $sexTitles;
    }
}