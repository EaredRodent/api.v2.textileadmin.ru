<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\SlsInvoice;

class SlsInvoiceController extends ActiveControllerExtended
{
	/** @var SlsInvoice $modelClass */
	public $modelClass = 'app\modules\v1\models\SlsInvoice';

	/**
	 * /v1/sls-invoice/accept
	 * @return array|\yii\db\ActiveRecord
	 */
	public function actionAccept()
	{
		return $this->modelClass::find()
			->where(['state' => $this->modelClass::stateAccept])
			->orderBy('sort')
			->all();
	}

	/**
	 * /v1/sls-invoice/part-pay
	 * @return array|\yii\db\ActiveRecord[]
	 */
	public function actionPartPay()
	{
		return $this->modelClass::find()
			->where(['state' => $this->modelClass::statePartPay])
			->orderBy('sort')
			->all();
	}

	public function actionGetWaitInvoices()
	{
		$resp = [];
		$dfdgs = [
            // АМ
            9 => 'Едуш',
            // ЕИ
            11 => 'Кривоносова',
            // Юра
            12 => 'Калашников',
            // Алена
            8 => 'Молодцова',
        ];
		foreach ($dfdgs as $key => $name) {

			$elm['name'] = $name;
			$elm['items'] = $this->modelClass::find()
				->where(['user_fk' => $key, 'state' => $this->modelClass::stateWait])
				->orderBy('sort')
				->all();
			$resp[] = $elm;
		}
		return $resp;
	}

}