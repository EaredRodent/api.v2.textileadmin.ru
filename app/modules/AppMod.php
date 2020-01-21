<?php


namespace app\modules;


use Yii;

class AppMod
{
    const ean13Prefix = '4563721';

    const domain = YII_ENV_PROD ? 'https://api.b2b.oxouno.ru' : 'http://api.textileadmin.loc';

    // Прикрепленные файлы для счетов в реестре пладежей
    const filesInvoiceAttachement = 'filesInvoiceAttachement';

    // Файлы счетов коотоые создает отдел продаж
    const filesInvoiceSlsDep = 'filesInvoiceSlsDep';

    // Файлы миниатюр для базовых продуктов
    const filesImageBaseProds = 'filesImageBaseProds';

    // Файлы миниатюр для продуктов с пост обработкой
    const filesImageProdsPrints = 'filesImageProdsPrints';

    // Файлы AAA юр.лиц
    const filesB2B_DOC1 = 'filesB2B_DOC1';

    // Файлы BBB юр.лиц
    const filesB2B_DOC2 = 'filesB2B_DOC2';

    // Файлы CCC юр.лиц
    const filesB2B_DOC3 = 'filesB2B_DOC3';

    // Файлы DDD юр.лиц
    const filesB2B_DOC4 = 'filesB2B_DOC4';

    // Файлы EEE юр.лиц
    const filesB2B_DOC5 = 'filesB2B_DOC5';

    // Файлы FFF юр.лиц
    const filesB2B_DOC6 = 'filesB2B_DOC6';

    // Типы документов для ИП
    const filesB2B_DocTypes_IP = [
        self::filesB2B_DOC1 => 'Документ 1',
        self::filesB2B_DOC2 => 'Документ 2',
        self::filesB2B_DOC3 => 'Документ 3'
    ];

    // Типы документов для ООО
    const filesB2B_DocTypes_OOO = [
        self::filesB2B_DOC4 => 'Документ 4',
        self::filesB2B_DOC5 => 'Документ 5',
        self::filesB2B_DOC6 => 'Документ 6'
    ];

    const filesRout = [
        self::filesInvoiceAttachement => '@app/../../textile/files/mail-doc',
        self::filesInvoiceSlsDep => '@app/../../textile/files/sls/invoices',
        self::filesImageBaseProds => '@app/../../textile/files/ref/prod',
        self::filesImageProdsPrints => '@app/../../textile/files/ref/prod-print',
        self::filesB2B_DOC1 => '@app/../../textile/files/b2b-docs/doc1',
        self::filesB2B_DOC2 => '@app/../../textile/files/b2b-docs/doc2',
        self::filesB2B_DOC3 => '@app/../../textile/files/b2b-docs/doc3',
        self::filesB2B_DOC4 => '@app/../../textile/files/b2b-docs/doc4',
        self::filesB2B_DOC5 => '@app/../../textile/files/b2b-docs/doc5',
        self::filesB2B_DOC6 => '@app/../../textile/files/b2b-docs/doc6',
    ];

    const pathProdPhoto = "@app/../../textile/files/ref/prod";

    // Настройки для работы с Телеграмм
    const tgProxySettings = 'socks5://proxyuser:pu123098@142.93.108.246:666';
    const tgBotOxounoB2b = '875076840:AAFqvdNNwwnRF8vKPAL8v4Yju9KQHfPoZ6k';
    const tgBotTextileAdmin = '403008129:AAHGQ_tjyZOoMYydV6jrFeVBBQEIWa7SDeA';
    const tgGroupOxounoB2b = YII_ENV_DEV ? '-391792716' : '-334517295';

    //const groupSales      = YII_ENV_DEV ? '-303512568' : '-237367400';

    const pathDocInvoice = "@app/../../textile/files/sls/invoices";
    const pathDocWaybill = "@app/../../textile/files/sls/torg12";

    // WS-сервер URL

    const wssUrl = 'ws://127.0.0.1:6001';

    // Секретный ключ для отправки сообщений по WS
    const wsSenderSecretKey = '149509e79053e4e2af391c01ab56fb6d646f6b434b1a3350532c4065061e3748';


}