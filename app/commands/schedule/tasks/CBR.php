<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 19.04.2019
 * Time: 10:19
 */

namespace app\commands\schedule\tasks;

use app\models\SlsCurrency;
use Exception;
use yii\httpclient\Client;

class CBR
{
    public function init()
    {
        $client = new Client();
        $date = date('d.m.Y');
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $date)
            ->send();

        if ($response->isOk) {
            $resp = $response->getData();
            $currency = [
                'USD' => ['Value' => '', 'Found' => false],
                'EUR' => ['Value' => '', 'Found' => false]
            ];

            foreach ($resp['Valute'] as $valute) {
                if (($valute['CharCode'] === 'USD') && (!$currency['USD']['Found'])) {
                    $currency['USD']['Value'] = $valute['Value'];
                    $currency['USD']['Found'] = true;
                }
                if (($valute['CharCode'] === 'EUR') && (!$currency['EUR']['Found'])) {
                    $currency['EUR']['Value'] = $valute['Value'];
                    $currency['EUR']['Found'] = true;
                }
                if ($currency['USD']['Found'] && $currency['EUR']['Found']) {
                    break;
                }
            }

            $date = date("Y-m-d", strtotime($date));

            $currency['USD'] = str_replace(',', '.', $currency['USD']);
            $currency['EUR'] = str_replace(',', '.', $currency['EUR']);

            if ($currency['USD']['Found'] && $currency['EUR']['Found']) {
                //USD

                $slsCurrency = SlsCurrency::find()
                    ->where(['date' => $date])
                    ->andWhere(['unit' => SlsCurrency::USD])
                    ->limit(1)
                    ->one();
                if (!$slsCurrency) {
                    $slsCurrency = new SlsCurrency();
                }
                $slsCurrency->date = $date;
                $slsCurrency->unit = SlsCurrency::USD;
                $slsCurrency->value = $currency['USD']['Value'];
                if (!$slsCurrency->save()) {
                    throw new Exception('CBR->init() Failed. Save model USD error.');
                }

                // EUR

                $slsCurrency = SlsCurrency::find()
                    ->where(['date' => $date])
                    ->andWhere(['unit' => SlsCurrency::EUR])
                    ->limit(1)
                    ->one();
                if (!$slsCurrency) {
                    $slsCurrency = new SlsCurrency();
                }
                $slsCurrency->date = $date;
                $slsCurrency->unit = SlsCurrency::EUR;
                $slsCurrency->value = $currency['EUR']['Value'];
                if (!$slsCurrency->save()) {
                    throw new Exception('CBR->init() Failed. Save model EUR error.');
                }
            } else {
                throw new Exception('CBR->init() Failed. Values not found.');
            }
        } else {
            throw new Exception('CBR->init() Failed.');
        }
    }
}
