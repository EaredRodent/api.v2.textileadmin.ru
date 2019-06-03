<?php

namespace app\modules\v1\models;

use app\gii\GiiAmfilesFile;

class AmfilesFile extends GiiAmfilesFile
{
	public function fields()
	{
		return array_merge(parent::fields(), [
			'is_shared' => function () {
			return $this->shared_key;
			}
		]);
	}

}
