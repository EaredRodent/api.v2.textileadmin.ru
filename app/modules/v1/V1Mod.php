<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 04.12.2018
 * Time: 19:06
 */

namespace app\modules\v1;

use Yii;
use yii\base\Module;

class V1Mod extends Module
{
	public $layout = false;
	public $cmdTables = [];
	public $cmdErrors = [];

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
		parent::init();
		Yii::$app->user->enableSession = false;
	}
}
