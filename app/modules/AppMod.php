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

    // Файлы миниатюр для продуктов с пост обработкой
    const filesImageProdsPrints = 'filesImageProdsPrints';


    // Типы документов для юр.лиц
    const filesB2BDocTypes = [
        self::filesB2BInnDocs,
        self::filesB2BInn2Docs,
        self::filesB2BInn3Docs,
    ];

    // Файлы ИНН юр.лиц
    const filesB2BInnDocs = 'filesB2BInnDocs';

    // Файлы ИНН2 юр.лиц
    const filesB2BInn2Docs = 'filesB2BInn2Docs';

    // Файлы ИНН3 юр.лиц
    const filesB2BInn3Docs = 'filesB2BInn3Docs';

    const filesRout = [
        self::filesInvoiceAttachement => '@app/../../textile/files/mail-doc',
        self::filesInvoiceSlsDep => '@app/../../textile/files/sls/invoices',
        self::filesImageBaseProds => '@app/../../textile/files/ref/prod',
        self::filesImageProdsPrints => '@app/../../textile/files/ref/prod-print',
        self::filesB2BInnDocs => '@app/../../textile/files/b2b-docs/inn',
        self::filesB2BInn2Docs => '@app/../../textile/files/b2b-docs/inn2',
        self::filesB2BInn3Docs => '@app/../../textile/files/b2b-docs/inn3'
    ];

    const pathProdPhoto = "@app/../../textile/files/ref/prod";

    // Настройки для работы с Телеграмм
    const tgProxySettings = 'socks5://proxyuser:pu123098@142.93.108.246:666';
    const tgBotOxounoB2b = '875076840:AAFqvdNNwwnRF8vKPAL8v4Yju9KQHfPoZ6k';
    const tgBotTextileAdmin = '403008129:AAHGQ_tjyZOoMYydV6jrFeVBBQEIWa7SDeA';
    const tgGroupOxounoB2b = YII_ENV_DEV ? '-391792716' : '-334517295';

    //const groupSales      = YII_ENV_DEV ? '-303512568' : '-237367400';


}