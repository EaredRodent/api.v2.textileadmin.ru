<?php
namespace app\services;


use yii\httpclient\Client;
use yii\httpclient\Response;

class ServReCAPTCHA
{
    public static function verify($reCaptchaToken = null)
    {
        if (YII_ENV_DEV) {
            return true;
        }

        $client = new Client();
        /** @var Response $response */
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://www.google.com/recaptcha/api/siteverify')
            ->setData([
                'secret' => '6LdlobwUAAAAALD-NSu1Fbp6lPiME79dXHV49deR',
                'response' => $reCaptchaToken
            ])
            ->send();

        if ($response->isOk) {
            $data = $response->getData();

            if (($data['success'] === true) &&
                ($data['score'] >= 0.5) &&
                ($data['hostname'] === 'b2b.oxouno.ru')) {
                return true;
            }
        }

        return false;
    }
}