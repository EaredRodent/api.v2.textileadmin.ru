<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:24
 */

namespace app\modules\v1\models\ref;


use app\extension\Sizes;
use app\gii\GiiRefArtBlank;
use app\modules\AppMod;
use app\modules\v1\classes\CardProd;
use app\objects\ProdWeight;

class RefArtBlank extends GiiRefArtBlank
{

    /**
     * @return array|false
     */
    public function fields()
    {
        // todo рефакторинг - выбор fields
        return array_merge(parent::fields(), [
            'titleStr' => function () {
                // ':' . $this->modelFk->sexFk->code_ru . ' ' .
//                return $this->modelFk->classFk->title . ' ' .
//                    $this->modelFk->title;

                return $this->modelFk->fashion;
            },
            'group' => function () { //
                return $this->modelFk->classFk->groupFk->title;
            },
            'class' => function () {
                return $this->modelFk->classFk->title;
            },
            'classOxo' => function () { //
                return $this->modelFk->classFk->oxouno;
            },
            'sex' => function () { //
                return $this->modelFk->sexFk->title;
            },
            'colorOxo' => function () { //
                return $this->themeFk->title_price;
            },
            'themeId' => function () { //
                return $this->theme_fk;
            },
            'themeStr' => function () { //
                return $this->themeFk->title;
            },
            'themeDescript' => function () { //
                return $this->themeFk->descript;
            },

            'printProd' => function () { //
                return 'Без принта';
            },
            'printOxo' => function () { //
                return 'Без принта';
            },
            'flagInPrice' => function () { //
                return $this->flag_price;
            },
            'assortment' => function () { //
                return $this->assortment;
            },
            'flagStopProd' => function () { //
                return $this->flag_stop_prod;
            },
            'fabricId' => function () { //
                return $this->fabric_type_fk;
            },
            'fabric' => function () { //
                return $this->fabricTypeFk->struct;
            },
            'fabricDensity' => function () { //
                return $this->fabricTypeFk->desity;
            },
            'fabricEpithets' => function () { //
                return $this->fabricTypeFk->epithets;
            },
            'fabricCare' => function () { //
                return $this->fabricTypeFk->calcCare();
            },
            'modelId' => function () { //
                return $this->model_fk;
            },
            'modelProdName' => function () { //
                return $this->modelFk->title;
            },
            'modelDescription' => function () { //
                return $this->modelFk->descript;
            },
            'modelEpithets' => function () { //
                return $this->modelFk->epithets;
            },
            'art' => function () {
                return 'OXO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
            },
            'photos' => function () {
                $resp['large'] = [];
                $resp['medium'] = [];
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

                    $fileName = str_pad($this->id, 4, '0', STR_PAD_LEFT) . '_' . $i . '.md.jpg';
                    $fullPath = $path . '/' . $fileName;
                    if (file_exists($fullPath)) {
                        $resp['medium'][] = AppMod::domain .
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


            'fabricTypeFk',
            'themeFk',
            'modelFk',
        ]);
    }

//
//    /**
//     * @return \yii\db\ActiveQuery
//     */
//    public function getRefArtBlanksTree()
//    {
//        return $this->hasMany(RefArtBlank::className(), ['model_fk' => 'id'])
//            ->joinWith('themeFk')
//            ->orderBy('favric_type_fk, ref_fabric_theme.title');
//    }

    /**
     * Вернуть id базового продукта
     * @return int
     */
    public function calcProdId()
    {
        return $this->id;
    }

    /**
     * Без принта print_fk = 1
     * @param int $printId
     * @return string
     */
    public function hClientArt($printId = 1)
    {
        $printStr = ($printId > 1) ? '-' . str_pad($printId, 3, '0', STR_PAD_LEFT) : '';
        return 'OXO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT) . $printStr;
    }

    /**
     * Наименования для накладной
     * @param $printFk RefProdPrint
     * @param $packFk RefProdPack
     * @return string
     */
    public function hTitleForDocs($printFk, $packFk)
    {
        $name = $this->modelFk->classFk->title_client;
        $sex = mb_strtolower(mb_substr($this->modelFk->sexFk->title, 0, 3));

        if ($this->modelFk->sexFk->code === "B") {
            $sex = ' для мальчиков';
            // @todo #костыль
            if ($this->modelFk->classFk->title === 'Свитшот') {
                $sex = ' детский';
            }
        }
        if ($this->modelFk->sexFk->code === "G") {
            $sex = ' для девочек';
            // @todo #костыль
            if ($this->modelFk->classFk->title === 'Свитшот') {
                $sex = ' детский';
            }
        }

        if ($this->modelFk->sexFk->code === "K") {
            $sex = $this->modelFk->classFk->kids_unisex;
        }
        if ($this->modelFk->sexFk->code === "U") {
            $sex = '';
        }

        $model = mb_strtoupper($this->modelFk->title);
        $theme = $this->themeFk->title;
        $art = $this->hClientArt($printFk->id);
        //$sizePart = ($sizeStr !== null) ? " {$sizeStr}" : ' *';
        $printPart = ($printFk->id > 1) ? "/{$printFk->title}" : '';
        $packPart = ($packFk->id > 1) ? " {$packFk->title}" : '';

        if ($this->modelFk->sexFk->code === "U") {
            return "{$name} {$model} ({$theme}{$printPart}) Арт: {$art}{$packPart}";
        } else {
            return "{$name}:{$sex}. {$model} ({$theme}{$printPart}) Арт: {$art}{$packPart}";
        }
    }


    /**
     * Вернуть тип размера - взрослый или детский
     */
    public function calcSizeType()
    {
        // todo - переделать взрослый детский в талицу
        $sexId = $this->modelFk->sex_fk;
        return (in_array($sexId, [1, 2, 5])) ? 'adults' : 'kids';
    }

    /**
     * Вернуть строковое обозначение размера
     * @param $fSize
     * @return
     */
    public function calcSizeStr($fSize)
    {
        $sexId = $this->modelFk->sex_fk;
        $type = (in_array($sexId, [1, 2, 5])) ? 'adults' : 'kids';

        if ($type === 'adults') {
            return Sizes::adults[$fSize];
        } else {
            return Sizes::kids[$fSize];
        }
    }

    /**
     * Получает массив с id новинок изделий
     * @param int $count
     * @return array
     */
    public static function calcNewProdIDs($count)
    {
        $newIDs = [];

        $newProds = RefArtBlank::find()
            ->where(['flag_price' => 1])
            ->orderBy('dt_create DESC')
            ->limit($count)
            ->all();

        foreach ($newProds as $newProd) {
            $newIDs[] = $newProd->id;
        }

        return $newIDs;
    }

    /**
     * Вернуть продукты по фильтрам
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
            ->joinWith('modelFk.sexFk')
            ->joinWith('modelFk.classFk')
            ->joinWith('modelFk.classFk.groupFk')
            ->joinWith('fabricTypeFk')
            ->joinWith('themeFk')
            ->filterWhere(['ref_art_blank.id' => $newProdIDs])
            ->andfilterWhere(['in', 'ref_blank_sex.title', $sexTitles])
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIDs])
            ->andFilterWhere(['in', 'ref_blank_class.oxouno', $classTags])
            ->andFilterWhere(['in', 'ref_blank_theme.title_price', $themeTags])
            ->andFilterWhere(['in', 'ref_fabric_type.type_price', $fabTypeTags])
            ->andWhere(['flag_price' => 1])
            ->all();
    }

    /**
     * @param $id
     * @return RefArtBlank|null
     */
    public static function readProd($id)
    {
        return self::findOne($id);
    }

}