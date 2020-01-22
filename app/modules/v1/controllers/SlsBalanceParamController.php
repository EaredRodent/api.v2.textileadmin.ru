<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 1/21/2020
 * Time: 4:28 PM
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsBalanceParam;
use yii\web\HttpException;

class SlsBalanceParamController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionGetAll = 'GET /v1/sls-balance-param/get-all';

    function actionGetAll()
    {
        $result = [];

        $result['os'] = SlsBalanceParam::find()
            ->where(['type' => 'os'])
            ->all();

        $result['loans'] = SlsBalanceParam::find()
            ->where(['type' => 'loans'])
            ->all();

        $result['osTotalSum'] = 0;

        foreach ($result['os'] as $os) {
            $result['osTotalSum'] += $os->value;
        }

        $result['loansTotalSum'] = 0;

        foreach ($result['loans'] as $loans) {
            $result['loansTotalSum'] += $loans->value;
        }
        return $result;
    }

    const actionCreateEdit = 'POST /v1/sls-balance-param/create-edit';

    public function actionCreateEdit($type, $name, $value, $id = null)
    {
        $param = null;

        if ($id) {
            $param = SlsBalanceParam::findOne(['id' => $id]);
        } else {
            $param = new SlsBalanceParam();
            $param->type = $type;
        }

        $param->name = $name;
        $param->value = $value;
        $param->save();

        return ['_result_' => 'success'];
    }

    const actionDeleteById = 'POST /v1/sls-balance-param/delete-by-id';

    public function actionDeleteById($id)
    {
        $param = SlsBalanceParam::findOne(['id' => $id]);
        if (!$param) {
            throw new HttpException(200, 'Запись уже удалена.', 200);
        }
        $param->delete();
        return ['_result_' => 'success'];
    }
}