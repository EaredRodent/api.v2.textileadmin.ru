<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 30.05.2019
 * Time: 12:50
 */

namespace app\modules\v1\classes;

use app\modules\v1\V1Mod;
use Yii;
use yii\db\ActiveRecord;


class ActiveRecordExtended extends ActiveRecord
{
    public function save($runValidation = true, $attributeNames = null)
    {
        /** @var V1Mod $module */
        $module = Yii::$app->getModule('v1');
        if (parent::save()) {
            $module->cmdTables[] = static::tableName();
            return true;
        } else {
            $errStr = '';
            $error = static::getFirstErrors();
            foreach ($error as $field => $err) {
                $errStr = static::tableName() . '.' . $field . ' = ' . $err;
            }
            $module->cmdErrors[] = $errStr;
            return false;
        }
    }

    public function delete()
    {
        /** @var V1Mod $module */
        $module = Yii::$app->getModule('v1');
        if (parent::delete()) {
            $module->cmdTables[] = static::tableName();
        } else {
            $errStr = '';
            $error = static::getFirstErrors();
            foreach ($error as $field => $err) {
                $errStr = static::tableName() . '.' . $field . ' = ' . $err;
            }
            $module->cmdErrors[] = $errStr;
        }
    }

    public static function get($id)
    {
        return static::findOne(['id' => (int)$id]);
    }

    public static function getAll($ids = null)
    {
        return static::find()
            ->filterWhere(['id' => $ids])
            ->all();
    }
}