<?php


namespace app\modules;


use Yii;

class AppMod
{


    const domain = YII_ENV_PROD ? 'https://api.textileadmin.ru' : 'http://api.textileadmin.loc';

    // Прикрепленные файлы для счетов в реестре пладежей
    const filesInvoiceAttachement = 'filesInvoiceAttachement';

    // Файлы счетов коотоые создает отдел продаж
    const filesInvoiceSlsDep = 'filesInvoiceSlsDep';

    // Файлы миниатюр для базовых продуктов
    const filesImageBaseProds = 'filesImageBaseProds';

    const filesRout = [
        self::filesInvoiceAttachement => '@app/../../textile/files/mail-doc',
        self::filesInvoiceSlsDep => '@app/../../textile/files/sls/invoices',
        self::filesImageBaseProds => '@app/../../textile/files/ref/prod',
    ];

    const pathProdPhoto = "@app/../../textile/files/ref/prod";
}