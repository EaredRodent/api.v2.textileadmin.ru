<?php
/** @var \app\commands\MarketController $this */
/** @var string $name */
/** @var string $company */

/** @var string $url */

use app\extension\Sizes;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankClass;

/** @var RefBlankClass[] $categories */
$categories = RefBlankClass::find()
    ->groupBy('tag')
    ->all();

/** @var RefArtBlank[] $prods */
$prods = RefArtBlank::find()
    ->where(['flag_price' => 1])
    ->all();
?>
<?= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<yml_catalog date="<?= $date ?>">
    <shop>
        <name><?= $name ?></name>
        <company><?= $company ?></company>
        <url><?= $url ?></url>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>
            <? foreach ($categories as $cat) : ?>
                <category id="<?= $cat->id ?>"><?= $cat->tag ?></category>
            <? endforeach; ?>
        </categories>
        <offers>
            <? foreach ($prods as $prod) : ?>
                <? $photos = $prod->fields()['photos']()['large']; ?>
                <? if ($photos): ?>
                    <? foreach (Sizes::prices as $field => $price) : ?>
                        <? if ($prod->$price) : ?>
                            <offer id="<?= $prod->id ?>">
                                <name><?= $prod->modelFk->fashion ?></name>
                                <vendor><?= $name ?></vendor>
                                <url><?= $url . '/item/' . $prod->id ?></url>
                                <price><?= $prod->$price ?></price>
                                <currencyId>RUR</currencyId>
                                <categoryId><?= $prod->modelFk->class_fk ?></categoryId>
                                <? foreach ($prod->fields()['photos']()['large'] as $photo) : ?>
                                    <picture><?= $photo ?></picture>
                                <? endforeach; ?>
                                <param name="Цвет"><?= $prod->themeFk->title ?></param>
                                <param name="Размер" unit="INT"><?= $field ?></param>
                                <param name="Пол">Женский</param>
                                <param name="Возраст">Взрослый</param>
                            </offer>
                        <? endif; ?>
                    <? endforeach; ?>
                <? endif; ?>
            <? endforeach; ?>
        </offers>
    </shop>
</yml_catalog>