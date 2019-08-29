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
            'titleStr' => function () {
                return $this->modelFk->classFk->title . ':' . $this->modelFk->sexFk->code_ru . ' ' .
                    $this->modelFk->title;
            },
            'art' => function () {
                return 'OXO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
            },
            'photos' => function () {
                $resp['large'] = [];
                $resp['small'] = [];

                $path = realpath(\Yii::getAlias(AppMod::filesRout[AppMod::filesImageBaseProds]));

                for ($i = 1; $i <= 4; $i++) {

                    // todo быдлокод

                    $fileName = str_pad($this->id, 4, '0', STR_PAD_LEFT) . '_' . $i . '.jpg';
                    $fullPath = $path . '/' . $fileName;
                    if (file_exists($fullPath)) {
                        $resp['large'][] = AppMod::domain .
                            '/v1/files/public/' . AppMod::filesImageBaseProds . '/' . $fileName;
                    }

                    $fileNameSmall = str_pad($this->id, 4, '0', STR_PAD_LEFT) . '_' . $i . '.sm.jpg';
                    $fullPathSmall = $path . '/' . $fileName;
                    if (file_exists($fullPathSmall)) {
                        $resp['small'][] = AppMod::domain .
                            '/v1/files/public/' . AppMod::filesImageBaseProds . '/' . $fileNameSmall;
                    }

                }
                return $resp;
            },
            'fabricTypeFk',
            'themeFk',
            'modelFk',
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