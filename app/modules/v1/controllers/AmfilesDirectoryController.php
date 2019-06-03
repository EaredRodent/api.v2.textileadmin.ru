<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 28.05.2019
 * Time: 13:51
 */

namespace app\modules\v1\controllers;


use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\AmfilesDirectory;
use Yii;

class AmfilesDirectoryController extends ActiveControllerExtended
{
    /** @var AmfilesDirectory $modelClass */
	public $modelClass = 'app\modules\v1\models\AmfilesDirectory';

	public function actions()
	{
		$actions = parent::actions();
		unset($actions['create']);
		return $actions;
	}

	public function actionCreate()
	{
		$name = Yii::$app->request->post('name');
		$dir = new $this->modelClass();
		$dir->name = $name;
		$dir->user_fk = Yii::$app->user->id;
		$dir->type = 'typeDir';
		$dir->save();
		return 'sgs';
	}

	public function actionUpdate()
	{
	}
}
