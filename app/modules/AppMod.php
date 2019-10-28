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

    const filesRout = [
        self::filesInvoiceAttachement => '@app/../../textile/files/mail-doc',
        self::filesInvoiceSlsDep => '@app/../../textile/files/sls/invoices',
        self::filesImageBaseProds => '@app/../../textile/files/ref/prod',
        self::filesImageProdsPrints => '@app/../../textile/files/ref/prod-print',
    ];

    const pathProdPhoto = "@app/../../textile/files/ref/prod";

    // Настройки для работы с Телеграмм
    const tgProxySettings = 'socks5://proxyuser:pu123098@142.93.108.246:666';
    const tgBotOxounoB2b = '875076840:AAFqvdNNwwnRF8vKPAL8v4Yju9KQHfPoZ6k';
    const tgBotTextileAdmin = '403008129:AAHGQ_tjyZOoMYydV6jrFeVBBQEIWa7SDeA';
    const tgGroupOxounoB2b = YII_ENV_DEV ? '-391792716' : '-334517295';

    //const groupSales      = YII_ENV_DEV ? '-303512568' : '-237367400';


}