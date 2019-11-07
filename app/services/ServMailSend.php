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

    /**
     * @param $email - адресат
     * @param $subject - тема письма
     * @param $body - тело в html
     * @return array
     */
    static function send($email, $subject, $body)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {

            // Настройки SMTP yandex
//            $mail->isSMTP();
//            $mail->CharSet = 'UTF-8';
//            $mail->SMTPAuth = true;
//            $mail->SMTPDebug = 0;
//            $mail->Host = 'ssl://smtp.yandex.ru';
//            $mail->Port = 465;
//            $mail->Username = 'invoice@textileadmin.ru';
//            $mail->Password = 'lYHTnEB7R2bNOlkHErrN';


            // Настройки SMTP 587 STARTTLS
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            $mail->Host = 'oxouno.ru';
            $mail->Username = 'b2b@oxouno.ru';
            $mail->Password = '876IwN61Lr';


            // От кого
//            $mail->setFrom('invoice@textileadmin.ru', 'invoice@textileadmin.ru');
            $mail->setFrom('b2b@oxouno.ru', 'B2B-кабинет OXOUNO');

            // Кому
            //$mail->addAddress('accnotfake@gmail.com');
            //$mail->addAddress('ralex@tsrz.biz');
            //$mail->addAddress('test-1psin@mail-tester.com');
            $mail->addAddress($email);

            // Тема письма
            //$mail->Subject = 'Тестовое письмо';
            $mail->Subject = $subject;

            // Тело письма
            //$body = '<p><strong>' . $text . '</strong></p>';
            $mail->msgHTML($body);

            // Приложение
            //$mail->addAttachment(__DIR__ . '/image.jpg');

            $mail->send();
            return ['resp' => 'ok'];

        } catch (Exception $e) {
            $str = $e->getMessage();
            return ['resp' => $str];
        }


    }
}