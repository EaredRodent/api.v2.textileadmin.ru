<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 04.12.2018
 * Time: 19:06
 */

namespace app\modules\v1;

use app\rbac\Rbac;

class V1Mod extends \yii\base\Module
{
	public $layout = false;

//	static $roles = [
//		Rbac::roleMaster => [
//
//		],
//		Rbac::roleEdush  => [
//
//		],
//	];


	function init()
	{
		parent::init(); // TODO: Change the autogenerated stub
		\Yii::$app->user->enableSession = false;
	}
}