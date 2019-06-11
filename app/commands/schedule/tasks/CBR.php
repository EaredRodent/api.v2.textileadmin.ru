<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 19.04.2019
 * Time: 10:19
 */

namespace app\commands\schedule\tasks;

use app\models\SlsCurrency;
use yii\httpclient\Client;


class CBR
{
   public function init()
   {
      $client = new Client();
      $response = $client->createRequest()
         ->setMethod('GET')
         ->setUrl('http://www.cbr.ru/scripts/XML_daily.asp')
         ->send();

      if ($response->isOk) {
         $resp = $response->getData();
         $date = '';
         $currency = [
            'USD' => ['Value' => '', 'Found' => false],
            'EUR' => ['Value' => '', 'Found' => false]
         ];

         $date = $resp['@attributes']['Date'];


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

         if ($date && $currency['USD']['Found'] && $currency['EUR']['Found']) {
            $slsCurrency = new SlsCurrency();
            $slsCurrency->date = $date;
            $slsCurrency->unit = 'USD';
            $slsCurrency->value = $currency['USD']['Value'];
            if (!$slsCurrency->save()) {
               echo 'CBR->init() Failed. Save model USD error.';
            }

            $slsCurrency = new SlsCurrency();
            $slsCurrency->date = $date;
            $slsCurrency->unit = 'EUR';
            $slsCurrency->value = $currency['EUR']['Value'];
            if (!$slsCurrency->save()) {
               echo 'CBR->init() Failed. Save model EUR error.';
            }
         } else {
            echo 'CBR->init() Failed. Bad date/value.';
         }
      } else {
         echo 'CBR->init() Failed.';
      }
   }
}
