<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 30.05.2019
 * Time: 12:50
 */

namespace app\modules\v1\classes;

use Yii;
use yii\db\ActiveRecord;


class ActiveRecordExtended extends ActiveRecord
{
	public function save($runValidation = true, $attributeNames = null)
	{
		if (parent::save()) {
			Yii::$app->app->cmdTables[] = static::tableName();
			return true;
		} else {
			$errStr = '';
			$error = static::getFirstErrors();
			foreach ($error as $field => $err) {
				$errStr = static::tableName() . '.' . $field . ' = ' . $err;
			}
			Yii::$app->app->cmdErrors[] = $errStr;
			return false;
		}
	}

	public function delete()
	{
		if (parent::delete()) {
			Yii::$app->app->cmdTables[] = static::tableName();
		} else {
			$errStr = '';
			$error = static::getFirstErrors();
			foreach ($error as $field => $err) {
				$errStr = static::tableName() . '.' . $field . ' = ' . $err;
			}
			Yii::$app->app->cmdErrors[] = $errStr;
		}
	}

	public static function readRecord($id)
	{
		return static::find()
			->where(['id' => (int)$id])
			->one();
	}

	public static function readRecords($ids = null)
	{
		return static::find()
			->filterWhere(['id' => $ids])
			->all();
	}
}