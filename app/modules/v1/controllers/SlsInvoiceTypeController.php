<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 2/20/2020
 * Time: 4:04 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsInvoiceType;

class SlsInvoiceTypeController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetAll = 'GET /v1/sls-invoice-type/get-all';

    /**
     * Вернуть все категории счетов
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetAll()
    {
        return SlsInvoiceType::find()->orderBy('sort')->all();
    }

    const actionCreateEdit = 'POST /v1/sls-invoice-type/create-edit';

    /**
     * Создать или редактировать тип счета
     * @param $name
     * @param $sort
     * @param int $id
     * @return array|\yii\db\ActiveRecord[]
     * @throws \yii\web\HttpException
     */
    public function actionCreateEdit($name, $sort, $id = 0)
    {
        if ($id) {
            $type = SlsInvoiceType::findOne(['id' => $id]);
        } else {
            $type = new SlsInvoiceType();
        }

        $type->sort = $sort;
        $type->name = $name;
        $type->save();

        return ['_result_' => 'success'];
    }

    const actionApplySortArrayForTypes = 'POST /v1/sls-invoice-type/apply-sort-array-for-types';

    /**
     * Задает sort для каждого типа соответственно массиву:
     * (значение массива - id типа, ключ массива - новый sort для него)
     * @param array $array
     * @return array
     * @throws \yii\web\HttpException
     */
    public function actionApplySortArrayForTypes(array $array)
    {
        foreach ($array as $sort => $typeID) {
            $type = SlsInvoiceType::findOne(['id' => $typeID]);
            $type->sort = $sort;
            $type->save();
        }

        return ['_result_' => 'success'];
    }

    const actionDeleteType = 'POST /v1/sls-invoice-type/delete-type';

    /**
     * Удаляет тип по его id
     * @param $id
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteType($id)
    {
        $type = SlsInvoiceType::findOne(['id' => $id]);
        /** @var SlsInvoiceType[] $typesUpper */
        $typesUpper = SlsInvoiceType::find()->where(['>', 'sort', $type->sort])->all();
        foreach ($typesUpper as $typeUpper) {
            $typeUpper->sort--;
            $typeUpper->save();
        }
        $type->delete();
        return ['_result_' => 'success'];
    }
}