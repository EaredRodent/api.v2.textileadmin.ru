<?php


namespace app\services;


use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * https://github.com/PHPMailer/PHPMailer
 *
 * composer require phpmailer/phpmailer
 *
 * Защита от спама https://www.mail-tester.com/
 *
 * Class ServMailSend
 * @package services
 */
class ServMailSend
{

    static function send($text)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {

            $mail->CharSet = 'UTF-8';

            // Настройки SMTP
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPDebug = 0;

            $mail->Host = 'ssl://smtp.yandex.ru';
            $mail->Port = 465;
            $mail->Username = 'invoice@textileadmin.ru';
            $mail->Password = 'lYHTnEB7R2bNOlkHErrN';

            // От кого
            $mail->setFrom('invoice@textileadmin.ru', 'invoice@textileadmin.ru');

            // Кому
            //$mail->addAddress('accnotfake@gmail.com');
            //$mail->addAddress('test-1psin@mail-tester.com');

            // Тема письма
            $mail->Subject = 'Тестовое письмо';

            // Тело письма
            $body = '<p><strong>' . $text . '</strong></p>';
            $mail->msgHTML($body);

            // Приложение
            //$mail->addAttachment(__DIR__ . '/image.jpg');

            $mail->send();
            return ['resp' => 'ok'];

        } catch (Exception $e) {
            return ['resp' => 'fail'];
        }


    }
}