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
use app\modules\v1\classes\CardProd;
use app\objects\Prices;

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
            'group' => function () {
                return $this->blankFk->modelFk->classFk->groupFk->title;
            },
            'class' => function () {
                return $this->blankFk->modelFk->classFk->title;
            },
            'classOxo' => function () {
                return $this->blankFk->modelFk->classFk->oxouno;
            },
            'sex' => function () { //
                return $this->blankFk->modelFk->sexFk->title;
            },
            'colorOxo' => function () {
                return $this->blankFk->themeFk->title_price;
            },
            'themeId' => function () { //
                return $this->blankFk->theme_fk;
            },
            'themeStr' => function () { //
                return $this->blankFk->themeFk->title;
            },
            'themeDescript' => function () { //
                return $this->blankFk->themeFk->descript;
            },
            'themePhoto' => function () { //
                return $this->blankFk->themeFk->hGetPhotoAddr();
            },
            'printProd' => function () { //
                return $this->printFk->title;
            },
            'printOxo' => function () { //
                return $this->printFk->oxouno;
            },
            'flagInPrice' => function () { //
                return $this->flag_price;
            },
            'assortment' => function () { //
                return $this->assortiment;
            },
            'flagStopProd' => function () { // todo flag_stop_prod убрать
                return $this->blankFk->flag_stop_prod;
            },
            'fabricId' => function () { //
                return $this->blankFk->fabric_type_fk;
            },
            'fabric' => function () { //
                return $this->blankFk->fabricTypeFk->struct;
            },
            'fabricDensity' => function () { //
                return $this->blankFk->fabricTypeFk->desity;
            },
            'fabricEpithets' => function () { //
                return $this->blankFk->fabricTypeFk->epithets;
            },
            'fabricCare' => function () { //
                return $this->blankFk->fabricTypeFk->calcCare();
            },
            'collection' => function () { //
                return ($this->collection_fk > 0) ? $this->collectionFk->name : '';
            },
            'collectionId' => function () { //
                return ($this->collection_fk > 0) ? $this->collection_fk : null;
            },
            'prodDescription' => function () { //
                //return ($this->descript_fk > 0) ? $this->descriptFk->descript : '';
                if (trim($this->printFk->epithets) && ($this->blankFk->descript_fk > 0)) {
                    return $this->blankFk->descriptFk->descript . "\r\n" . $this->printFk->epithets;
                } else {
                    return '';
                }
            },
            'modelId' => function () { //
                return $this->blankFk->model_fk;
            },
            'modelProdName' => function () { //
                return $this->blankFk->modelFk->title;
            },
            'modelDescription' => function () { //
                return $this->blankFk->modelFk->descript;
            },
            'modelEpithets' => function () { //
                return $this->blankFk->modelFk->epithets;
            },
            'photos' => function () {
                $resp['large'] = [];
                $resp['medium'] = [];
                $resp['small'] = [];
                $resp['baseFilePath'] = '';

                $path = realpath(\Yii::getAlias(AppMod::filesRout[AppMod::filesImageProdsPrints]));

                for ($i = 1; $i <= 4; $i++) {

                    // todo быдлокод

                    $fileName = str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($this->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $i . '.jpg';
                    $fullPath = $path . '/' . $fileName;
                    if (file_exists($fullPath)) {
                        $resp['large'][] = CURRENT_API_URL .
                            '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;

                        if(!$resp['baseFilePath']) {
                            $resp['baseFilePath'] = $fullPath;
                        }
                    }



                    $fileName = str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($this->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $i . '.md.jpg';
                    $fullPath = $path . '/' . $fileName;
                    if (file_exists($fullPath)) {
                        $resp['medium'][] = CURRENT_API_URL .
                            '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;
                    }

                    $fileNameSmall = str_pad($this->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($this->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $i . '.sm.jpg';
                    $fullPathSmall = $path . '/' . $fileName;
                    if (file_exists($fullPathSmall)) {
                        $resp['small'][] = CURRENT_API_URL .
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
            'blankFk',
            'printFk',
            'discount' => function () {
                return $this->discount;
            },
            'packSizes' => function () {
                $sizes = $this->blankFk->modelFk->classFk->pack_size;
                $sizesArr = explode('x', $sizes);
                if (count($sizesArr) != 3) {
                    $sizesArr = [null, null, null];
                }
                return [
                    'width' => (int)$sizesArr[0],
                    'length' => (int)$sizesArr[1],
                    'height' => (int)$sizesArr[2],
                ];
            },

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
     * @return array
     */
    public static function calcNewProdIDs()
    {
        $newIDs = [];
        $dateStart = date('Y-m-d H:i:s', strtotime('-30 day'));

        $newProds = self::find()
            ->where(['flag_price' => 1])
            ->andWhere(['>=', 'ts_create', $dateStart])
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
    public static function readFilterProds($newProdIDs, $discountOnly, $sexTitles, $collectionIDs, $groupIDs, $classTags, $themeTags, $fabTypeTags)
    {
        $activeQuery = self::find()
            ->joinWith('blankFk.modelFk.sexFk')
            ->joinWith('blankFk.modelFk.classFk')
            ->joinWith('blankFk.modelFk.classFk.groupFk')
            ->joinWith('blankFk.fabricTypeFk')
            ->joinWith('blankFk.themeFk')
            ->joinWith('collectionFk')
            ->filterWhere(['ref_product_print.id' => $newProdIDs])
            ->andfilterWhere(['in', 'ref_blank_sex.title', $sexTitles])
            ->andFilterWhere(['in', 'ref_collection.id', $collectionIDs])
            ->andfilterWhere(['in', 'ref_blank_group.id', $groupIDs])
            ->andFilterWhere(['in', 'ref_blank_class.oxouno', $classTags])
            ->andFilterWhere(['in', 'ref_blank_theme.title_price', $themeTags])
            ->andFilterWhere(['in', 'ref_fabric_type.type_price', $fabTypeTags])
            ->andWhere(['ref_product_print.flag_price' => 1]);

        if ($discountOnly) {
            $activeQuery->andWhere(['>', 'ref_product_print.discount', 0]);
        }

        return $activeQuery->all();
    }

    /**
     * @param $id
     * @return RefProductPrint|null
     */
    public static function readProd($prodId, $printId)
    {
        return self::findOne(['blank_fk' => $prodId, 'print_fk' => $printId]);
    }

    /**
     * @param $refProductPrints
     * @return array|\yii\db\ActiveRecord[]
     */
    static function readForPrice($groupId, $categoryId, $sexId, $refProductPrints, $mode)
    {
        $prods = self::find()
            ->joinWith('blankFk.modelFk.sexFk')
            ->joinWith('collectionFk.divFk')
            ->joinWith('blankFk.modelFk.classFk.groupFk')
            ->where([
                'ref_product_print.flag_price' => 1,
                'ref_blank_model.sex_fk' => $sexId
            ]);

        if ($mode === 'assort') {
            $prods = $prods->andWhere(['ref_collect_div.id' => $categoryId]);
        }
        if ($mode === 'discount') {
            $prods = $prods->andWhere(['ref_blank_group.id' => $groupId]);
        }

        $prods = $prods->orderBy('blank_fk DESC, print_fk DESC')->all();

        /** @var RefProductPrint[] $prods */
        $prods = array_filter($prods, function ($prod) use ($refProductPrints) {
            /** @var RefProductPrint $prod */

            $addToProds = false;

            foreach ($refProductPrints as $refProductPrint) {
                if (($prod->blank_fk === $refProductPrint[0]) && ($prod->print_fk === $refProductPrint[1])) {
                    $addToProds = true;
                    break;
                }
            }

            return $addToProds;
        });

        return $prods;
    }

    /**
     * Вернуть продукты по фильтрам
     * @param $categoryID
     * @param $collectionID
     * @param $sexTitles
     * @param $modelID
     * @param $discountNumber
     * @param $groupID
     * @return array|self[]
     */
    static public function readFilterProds2($categoryID, $collectionID, $sexTitles, $modelID, $discountNumber, $groupID)
    {
        $activeQuery = self::find()
            ->joinWith('blankFk.modelFk.sexFk')
            ->joinWith('blankFk.modelFk.classFk.groupFk')
            ->joinWith('blankFk.fabricTypeFk')
            ->joinWith('blankFk.themeFk')
            ->joinWith('collectionFk.divFk')
            ->filterWhere(['ref_collect_div.id' => $categoryID])
            ->andFilterWhere(['ref_collection.id' => $collectionID])
            ->andfilterWhere(['ref_blank_sex.title' => $sexTitles])
            ->andfilterWhere(['ref_blank_model.id' => $modelID])
            ->andFilterWhere(['ref_product_print.discount' => $discountNumber])
            ->andFilterWhere(['ref_blank_group.id' => $groupID])
            ->andWhere(['ref_product_print.flag_price' => 1]);

        if ($discountNumber === null) {
            $activeQuery = $activeQuery->andWhere('ref_product_print.collection_fk IS NOT NULL');
        } else {
            $activeQuery = $activeQuery->andWhere('ref_product_print.collection_fk IS NULL');
        }

        return $activeQuery->all();
    }
}
