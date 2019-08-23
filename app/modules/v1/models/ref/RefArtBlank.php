<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\gii\GiiRefArtBlank;

class RefArtBlank extends GiiRefArtBlank
{

    /**
     * @return array|false
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
            'art' => function() {
                return 'OXO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
            },
            //'fabricTypeFk',
            //'themeFk',
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
}