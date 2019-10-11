<?php


namespace app\services;


use app\modules\AppMod;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Отправить сообщение от имени бота в выбранную группу
 * @package services
 */
class ServTelegramSend
{

    /**
     * @param $botToken
     * @param $chatId
     * @param $message
     * @return bool|string
     */
    static function send($botToken, $chatId, $message)
    {

        $messageUrl = rawurlencode($message);

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage?disable_web_page_preview=true&" .
            "chat_id={$chatId}&parse_mode=html&text={$messageUrl}";

        $ch = curl_init();

        if (!empty(AppMod::tgProxySettings)) {
            curl_setopt($ch, CURLOPT_PROXY, AppMod::tgProxySettings);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        return curl_exec($ch);
    }
}