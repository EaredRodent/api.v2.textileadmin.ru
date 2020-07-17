<?php


namespace app\modules;


use Yii;

class AppMod
{
    const ean13Prefix = '4563721';

    const B2BAPIDomain = YII_ENV_PROD ? 'https://api.b2b.oxouno.ru' : 'http://api.textileadmin.loc';
    const B2BDomain = YII_ENV_PROD ? 'https://b2b.oxouno.ru' : 'http://localhost:3020';

    const fileToGitJson = '@app/web/deploy/deploy.json';

    // Прикрепленные файлы для счетов в реестре пладежей
    const filesInvoiceAttachement = 'filesInvoiceAttachement';

    // Файлы счетов коотоые создает отдел продаж
    const filesInvoiceSlsDep = 'filesInvoiceSlsDep';

    // Файлы миниатюр для базовых продуктов
    const filesImageBaseProds = 'filesImageBaseProds';

    // Файлы миниатюр для продуктов с пост обработкой
    const filesImageProdsPrints = 'filesImageProdsPrints';

    // Файлы фотографий декора 100x100
    // http://api.textileadmin.loc/v1/files/public/filesImageThemes/theme_148.jpg
    const filesImageThemes = 'filesImageThemes';

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

    // Сгенерированные прайс-листы для B2B
    const filesB2B_Prices = 'filesB2B_Prices';

    // Архив с сетом изображений для /outlook
    const filesB2B_OutlookArchive = 'filesB2B_OutlookArchive';

    // Сет изображений для /outlook
    const filesB2B_OutlookImgSet = 'filesB2B_OutlookImgSet';

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
        self::filesImageThemes => '@app/../../textile/files/ref/themes',
        self::filesB2B_DOC1 => '@app/../../textile/files/b2b-docs/doc1',
        self::filesB2B_DOC2 => '@app/../../textile/files/b2b-docs/doc2',
        self::filesB2B_DOC3 => '@app/../../textile/files/b2b-docs/doc3',
        self::filesB2B_DOC4 => '@app/../../textile/files/b2b-docs/doc4',
        self::filesB2B_DOC5 => '@app/../../textile/files/b2b-docs/doc5',
        self::filesB2B_DOC6 => '@app/../../textile/files/b2b-docs/doc6',
        self::filesB2B_Prices => '@app/../../textile/files/b2b-prices',
        self::filesB2B_OutlookImgSet => '@app/../../textile/files/b2b-outlook/img-set',
        self::filesB2B_OutlookArchive => '@app/../../textile/files/b2b-outlook/archive'
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

    // Authorization header для авторизованных запросов к GitHub API от лица x3RABBITx3
    const gitHubAuthorizationHeader = 'Basic cmFiYml0Z2l0OTBAbWFpbC5ydTpmZDhqSWlmOGZpSWR6';

    // Секретный ключ для генерации deploy.json
    const metaGenerateSecretKey = 'IuiKnzwda6xFtjeTd92K';
}
