<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\extension\Sizes;
use app\gii\GiiRefBlankClass;
use app\gii\GiiRefProductPrint;
use app\modules\AppMod;
use app\modules\v1\classes\ActiveRecordExtended;

/**
 * Class RefBlankClass
 * @property RefBlankModel[] $refBlankModelsTree
 */
class RefProductPrint extends GiiRefProductPrint
{

    /**
     * @return array|false
     */
    public function fields()
    {
        return array_merge(parent::fields(), [

            'titleStr' => function () {
                return $this->blankFk->modelFk->fashion;
            },

            'art' => function () {
                return 'OXO-' . str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                    str_pad($this->print_fk, 3, '0', STR_PAD_LEFT);
            },
            'class' => function () {
                return $this->blankFk->modelFk->classFk->title;
            },

            'photos' => function () {
                $resp['large'] = [];
                $resp['medium'] = [];
                $resp['small'] = [];

                $path = realpath(\Yii::getAlias(AppMod::filesRout[AppMod::filesImageProdsPrints]));

                for ($i = 1; $i <= 4; $i++) {

                    // todo быдлокод

                    $fileName = str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($this->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $i . '.jpg';
                    $fullPath = $path . '/' . $fileName;
                    if (file_exists($fullPath)) {
                        $resp['large'][] = AppMod::domain .
                            '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;
                    }

                    $fileName = str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($this->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $i . '.md.jpg';
                    $fullPath = $path . '/' . $fileName;
                    if (file_exists($fullPath)) {
                        $resp['medium'][] = AppMod::domain .
                            '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;
                    }

                    $fileNameSmall = str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($this->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $i . '.sm.jpg';
                    $fullPathSmall = $path . '/' . $fileName;
                    if (file_exists($fullPathSmall)) {
                        $resp['small'][] = AppMod::domain .
                            '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileNameSmall;
                    }

                }
                return $resp;
            },

            'minPrice' => function () {
                foreach (Sizes::prices as $fPrice) {
                    if ($this->$fPrice > 0) {
                        return $this->$fPrice;
                    }
                }
                return null;
            },
            'sizes' => function () {
                $resp = [];
                $typeSize = $this->calcSizeType();
                $sizesStr = Sizes::typeCompare[$typeSize];

                foreach (Sizes::prices as $fSize => $fPrice) {
                    if ($this->$fPrice > 0) {
                        $resp[] = [
                            'size' => $sizesStr[$fSize],
                            'price' => $this->$fPrice,
                        ];
                    }
                }
                return $resp;
            },


        ]);
    }


    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'blankFk',
            'printFk'
        ]);
    }

    /**
     * Вернуть id базового продукта
     * @return int
     */
    public function calcProdId()
    {
        return $this->blank_fk;
    }

    /**
     * Вернуть тип размера - взрослый или детский
     */
    public function calcSizeType()
    {
        // todo - переделать взрослый детский в талицу
        $sexId = $this->blankFk->modelFk->sex_fk;
        return (in_array($sexId, [1, 2, 5])) ? 'adults' : 'kids';
    }

    /**
     * Получает массив с id новинок изделий с принтом
     * @param int $count
     * @return array
     */
    public static function calcNewProdIDs($count)
    {
        $newIDs = [];

        $newProds = self::find()
            ->where(['flag_price' => 1])
            ->orderBy('ts_create DESC')
            ->limit($count)
            ->all();

        foreach ($newProds as $newProd) {
            $newIDs[] = $newProd->id;
        }

        return $newIDs;
    }

    /**
     * Вернуть продукты с принтом по фильтрам
     * @param $newProdIDs
     * @param $sexTitles
     * @param $groupIDs
     * @param $classTags
     * @param $themeTags
     * @param $fabTypeTags
     * @return array|self[]
     */
    public static function readFilterProds($newProdIDs, $sexTitles, $groupIDs, $classTags, $themeTags, $fabTypeTags)
    {
        return self::find()
            ->joinWith('blankFk.modelFk.sexFk')
            ->joinWith('blankFk.modelFk.classFk')
            ->joinWith('blankFk.modelFk.classFk.groupFk')
            ->joinWith('blankFk.fabricTypeFk')
            ->joinWith('blankFk.themeFk')

            ->filterWhere(['ref_product_print.id' => $newProdIDs])
            ->andfilterWhere(['in', 'ref_blank_sex.title', $sexTitles])
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIDs])
            ->andFilterWhere(['in', 'ref_blank_class.oxouno', $classTags])
            ->andFilterWhere(['in', 'ref_blank_theme.title_price', $themeTags])
            ->andFilterWhere(['in', 'ref_fabric_type.type_price', $fabTypeTags])
            ->andWhere(['ref_product_print.flag_price' => 1])
            ->all();
    }
}