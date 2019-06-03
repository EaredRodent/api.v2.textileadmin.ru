<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 28.05.2019
 * Time: 13:51
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\AmfilesFile;
use Yii;

/**
 * Class AmfilesFileController
 * @package app\modules\v1\controllers
 */
class AmfilesFileController extends ActiveControllerExtended
{
    /** @var AmfilesFile $modelClass */
	public $modelClass = 'app\modules\v1\models\AmfilesFile';

	public function actions()
	{
		$actions = parent::actions();
		unset($actions['update']);
		return $actions;
	}

	public function actionUpdate()
	{
		$id = Yii::$app->request->post('id');
		$isShared = Yii::$app->request->post('is_shared');
		$name = Yii::$app->request->post('name');
		$file = $this->modelClass::findOne(['id' => $id]);
		$file->name = $name;
		if ($isShared) {
			$nonce = 0;
			do {
				$file->shared_key = mb_strimwidth(md5($file->id . $nonce++), 0, 16);
			} while (AmfilesFile::findOne(['shared_key' => $file->shared_key]));
		} else {
			$file->shared_key = '';
		}
		$file->save();
	}
}
