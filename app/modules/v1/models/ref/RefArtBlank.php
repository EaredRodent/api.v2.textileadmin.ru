<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefArtBlank;
use app\modules\AppMod;

class RefArtBlank extends GiiRefArtBlank
{

    /**
     * @return array|false
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
            'art' => function () {
                return 'OXO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
            },
            'photos' => function () {
                $resp = [];
                for ($i = 1; $i <= 4; $i++) {
                    $fileName = str_pad($this->id, 4, '0', STR_PAD_LEFT) . '_' . $i . '.jpg';
                    $pathName = realpath(\Yii::getAlias(AppMod::pathProdPhoto) . '/' . $fileName);
                    //$fName2 = realpath($fName);
                    if (file_exists($pathName)) {
                        $resp[] = $pathName;
                    }
                    //$modelArt = $prod->modelFk->hArt();
                    //$fabricArt = $prod->fabricTypeFk->hArt();
                    //$name = "{$curArt}_{$i}";
                }
                return $resp;
            },
            //'fabricTypeFk',
            //'themeFk',
            //'modelFk',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefArtBlanksTree()
    {
        return $this->hasMany(RefArtBlank::className(), ['model_fk' => 'id'])
            ->joinWith('themeFk')
            ->orderBy('favric_type_fk, ref_fabric_theme.title');
    }

    public function hArt()
    {
        return 'OXO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);

    }
}