<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 07.02.2019
 * Time: 11:07
 */

namespace app\modules\v1\controllers;

use app\modules\v1\classes\ActiveControllerExtended;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\HttpException;
use Yii;

/**
 * Типы разрешений
 * - роль     (ROLE_...)
 * - страница (PAGE_...)
 * - rest api (API_...)
 * Class RoleManagerController
 * @package app\modules\v1\controllers
 */
class AuthItemController extends ActiveControllerExtended
{
	public $modelClass = '';

	public function actions()
	{
		$actions = parent::actions();
		return $actions;
	}

	public function actionBrowseRoles()
	{
		$id = 0;
		function FD($childrens, &$id = 0, $lvl = 0)
		{
			$childrenLvl = $lvl + 1;
			$arr = [];
			foreach ($childrens as $children) {
				$selfChildrens = Yii::$app->authManager->getChildren($children->name);
				$jsonItem = [
					'id'   => $id++,
					'lvl'  => $lvl,
					'type' => $children instanceof Role ? 'role' : 'permission',
					'name' => $children->name];
				if ($selfChildrens) {
					$jsonItem['children'] = FD($selfChildrens, $id, $childrenLvl);
				}
				$arr[] = $jsonItem;
			}
			return $arr;
		}

		$roles = Yii::$app->authManager->getRoles();
		$permissions = Yii::$app->authManager->getPermissions();
		$rolesTree = FD($roles, $id);
		$permissionsTree = FD($permissions, $id);
		$tree = [
			'roles'       => $rolesTree,
			'permissions' => $permissionsTree
		];
		return $tree;
	}

	public function actionCreateRole()
	{
		$post = Yii::$app->request->post();
		$role = new Role();
		$role->name = $post['name'];
		$role->description = $post['description'];
		if (Yii::$app->authManager->add($role)) {
			Yii::$app->response->statusCode = 201;
		} else {
			throw new HttpException(400);
		}
	}

	public function actionCreatePermission()
	{
		$post = Yii::$app->request->post();
		$permission = new Permission();
		$permission->name = $post['name'];
		$permission->description = $post['description'];
		if (Yii::$app->authManager->add($permission)) {
			Yii::$app->response->statusCode = 201;
		} else {
			throw new HttpException(400);
		}
	}

	public function actionExtendRoleByRole()
	{
		$post = Yii::$app->request->post();
		if (!Yii::$app->authManager->addChild(
			Yii::$app->authManager->getRole($post['parent']),
			Yii::$app->authManager->getRole($post['child'])
		)) {
			throw new HttpException(400);
		}
	}

	public function actionExtendRoleByPermission()
	{
		$post = Yii::$app->request->post();
		if (!Yii::$app->authManager->addChild(
			Yii::$app->authManager->getRole($post['parent']),
			Yii::$app->authManager->getPermission($post['child'])
		)) {
			throw new HttpException(400);
		}
	}

	public function actionExtendPermissionByPermission()
	{
		$post = Yii::$app->request->post();
		if (!Yii::$app->authManager->addChild(
			Yii::$app->authManager->getPermission($post['parent']),
			Yii::$app->authManager->getPermission($post['child'])
		)) {
			throw new HttpException(400);
		}
	}

	public function actionRevokeChild()
	{
		$post = Yii::$app->request->post();

		$parent = Yii::$app->authManager->getRole($post['parent']);
		if (!$parent) {
			$parent = Yii::$app->authManager->getPermission($post['parent']);
		}

		$child = Yii::$app->authManager->getRole($post['child']);
		if (!$child) {
			$child = Yii::$app->authManager->getPermission($post['child']);
		}

		if (!Yii::$app->authManager->removeChild($parent, $child)) {
			throw new HttpException(400);
		}
	}

	public function actionRemove()
	{
		$post = Yii::$app->request->post();
		$item = Yii::$app->authManager->getRole($post['name']);
		if (!$item) {
			$item = Yii::$app->authManager->getPermission($post['name']);
		}
		if (!Yii::$app->authManager->remove($item)) {
			throw new HttpException(400);
		}
	}
}