<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;

use app\extension\Sizes;
use app\gii\GiiRefBlankModel;

class RefBlankModel extends GiiRefBlankModel
{
    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'sexFk',
            'classFk'
        ]);
    }

    /**
     * Является ли модель детской
     * @return bool
     */
    public function isChildModel()
    {
        if ($this->sex_fk == 3 || $this->sex_fk == 4 || $this->sex_fk == 6) {
            return true;
        } else {
            return false;
        }
    }

    public function hCode()
    {
        return str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @return string - "Шорты:муж. МОДЕЛЬ 1"
     */
    public function hModelTitleShort6()
    {
        $sex = mb_strtolower($this->sexFk->code_ru);
        $model = mb_strtoupper($this->title);
        return "{$this->classFk->title}:{$sex}. {$model}";
    }

    /**
     * Вернуть строковое обозначение размера
     * @param $fieldSize
     * @return mixed
     */
    public function hSizeStr($fieldSize)
    {
        //if ($this->classFk->groupFk->flag_child_size) {
        if ($this->isChildModel()) {
            return Sizes::kids[$fieldSize];
        } else {
            return Sizes::adults[$fieldSize];
        }
    }
}