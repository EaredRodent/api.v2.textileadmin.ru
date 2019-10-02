<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 10/1/2019
 * Time: 10:41 AM
 */

namespace app\commands;


use yii\console\Controller;

class MarketController extends Controller
{
    // Shop elements (required)

    /*
     * Короткое название магазина.
     * В названии нельзя использовать слова, которые не относятся к наименованию магазина (например «лучший», «дешевый»), указывать номер телефона и т. п.
     * Название магазина должно совпадать с фактическим названием, которое публикуется на сайте.
     * Если требование не соблюдается, Яндекс.Маркет может самостоятельно изменить название без уведомления магазина.
     */
    private $name = 'OXO.UNO';

    /*
     * Полное наименование компании, владеющей магазином.
     * Не публикуется, используется для внутренней идентификации.
     */
    private $company = 'OXO.UNO';

    /*
     * URL главной страницы магазина.
     * Допускаются кириллические ссылки.
     */
    private $url = 'https://oxouno.ru/ru';

    public function actionIndex()
    {
        $dst = dirname(__FILE__, 2) . '/web/xml';

        $f = fopen($dst .'/list.xml', 'w');
        fwrite($f, $this->buildYml());
        fclose($f);
    }

    private function buildYml()
    {
        $date = date('Y-m-d h:m');
        $name = $this->name;
        $company = $this->company;
        $url = $this->url;
        return $this->renderPartial('shop',
            compact('date', 'name', 'company', 'url', 'currencies', 'categories', 'offers'));
    }
}