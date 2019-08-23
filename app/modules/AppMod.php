<?php


namespace app\modules;


use Yii;

class AppMod
{
    /**
     * Прикрепленные файлы для счетов в реестре пладежей
     */
    const filesInvoiceAttachement = 'filesInvoiceAttachement';

    /**
     * Файлы счетов коотоые создает отдел продаж
     */
    const filesInvoiceSlsDep = 'filesInvoiceSlsDep';

    const filesRout = [
        self::filesInvoiceAttachement => '@app/../../textile/files/mail-doc',
        self::filesInvoiceSlsDep => '@app/../../textile/files/sls/invoices',
    ];

    const pathProdPhoto = "@app/../../textile/files/ref/prod";
}