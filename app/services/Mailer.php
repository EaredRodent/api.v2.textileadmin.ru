<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 3/25/2020
 * Time: 12:40 PM
 */

namespace app\services;


use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer {
    /**
     * Save email to a folder (via IMAP)
     *
     * This function will open an IMAP stream using the email
     * credentials previously specified, and will save the email
     * to a specified folder. Parameter is the folder name (ie, Sent)
     * if nothing was specified it will be saved in the inbox.
     *
     * @author David Tkachuk <http://davidrockin.com/>
     */
    public function copyToFolder($folderPath = null) {
        $message = $this->MIMEHeader . $this->MIMEBody;
        $imapStream = imap_open("{" . 'mail.nic.ru/ssl' . "}" . $folderPath , $this->Username, $this->Password);
        imap_append($imapStream, "{" . 'mail.nic.ru/ssl' . "}" . $folderPath, $message);
        imap_close($imapStream);
    }
}