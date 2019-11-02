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

    // Файлы AAA юр.лиц
    const filesB2B_AAA = 'filesB2B_AAA';

    // Файлы BBB юр.лиц
    const filesB2B_BBB = 'filesB2B_BBB';

    // Файлы CCC юр.лиц
    const filesB2B_CCC = 'filesB2B_CCC';

    // Файлы DDD юр.лиц
    const filesB2B_DDD = 'filesB2B_DDD';

    // Файлы EEE юр.лиц
    const filesB2B_EEE = 'filesB2B_EEE';

    // Файлы FFF юр.лиц
    const filesB2B_FFF = 'filesB2B_FFF';

    // Типы документов для ИП
    const filesB2B_DocTypes_IP = [
        self::filesB2B_AAA => 'ААА',
        self::filesB2B_BBB => 'BBB',
        self::filesB2B_CCC => 'CCC'
    ];

    // Типы документов для ООО
    const filesB2B_DocTypes_OOO = [
        self::filesB2B_DDD => 'DDD',
        self::filesB2B_EEE => 'EEE',
        self::filesB2B_FFF => 'FFF'
    ];

    const filesRout = [
        self::filesInvoiceAttachement => '@app/../../textile/files/mail-doc',
        self::filesInvoiceSlsDep => '@app/../../textile/files/sls/invoices',
        self::filesImageBaseProds => '@app/../../textile/files/ref/prod',
        self::filesImageProdsPrints => '@app/../../textile/files/ref/prod-print',
        self::filesB2B_AAA => '@app/../../textile/files/b2b-docs/aaa',
        self::filesB2B_BBB => '@app/../../textile/files/b2b-docs/bbb',
        self::filesB2B_CCC => '@app/../../textile/files/b2b-docs/ccc',
        self::filesB2B_DDD => '@app/../../textile/files/b2b-docs/ddd',
        self::filesB2B_EEE => '@app/../../textile/files/b2b-docs/eee',
        self::filesB2B_FFF => '@app/../../textile/files/b2b-docs/fff',
    ];

    const pathProdPhoto = "@app/../../textile/files/ref/prod";

    // Настройки для работы с Телеграмм
    const tgProxySettings = 'socks5://proxyuser:pu123098@142.93.108.246:666';
    const tgBotOxounoB2b = '875076840:AAFqvdNNwwnRF8vKPAL8v4Yju9KQHfPoZ6k';
    const tgBotTextileAdmin = '403008129:AAHGQ_tjyZOoMYydV6jrFeVBBQEIWa7SDeA';
    const tgGroupOxounoB2b = YII_ENV_DEV ? '-391792716' : '-334517295';

    //const groupSales      = YII_ENV_DEV ? '-303512568' : '-237367400';


}