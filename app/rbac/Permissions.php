<?php


namespace app\rbac;


use app\modules\v1\controllers\AnxUserController;
use app\modules\v1\controllers\BaseController;
use app\modules\v1\controllers\CardProdController;
use app\modules\v1\controllers\LogEventController;
use app\modules\v1\controllers\OxounoApiController;
use app\modules\v1\controllers\PrStorProdController;
use app\modules\v1\controllers\RefArtBlankController;
use app\modules\v1\controllers\RefBlankClassController;
use app\modules\v1\controllers\RefBlankGroupController;
use app\modules\v1\controllers\RefBlankModelController;
use app\modules\v1\controllers\RefBlankSexController;
use app\modules\v1\controllers\RefBlankThemeController;
use app\modules\v1\controllers\RefFabricTypeController;
use app\modules\v1\controllers\RefProductPrintController;
use app\modules\v1\controllers\SlsClientController;
use app\modules\v1\controllers\SlsCurrencyController;
use app\modules\v1\controllers\SlsInvoiceController;
use app\modules\v1\controllers\SlsItemController;
use app\modules\v1\controllers\SlsMessageController;
use app\modules\v1\controllers\SlsMessageStateController;
use app\modules\v1\controllers\SlsMoneyController;
use app\modules\v1\controllers\SlsOrderController;
use app\modules\v1\controllers\SlsOrgController;
use app\modules\v1\controllers\SlsPayItemController;
use app\modules\v1\controllers\TestController;
use app\modules\v1\controllers\V3BoxController;
use app\modules\v1\controllers\V3InvoiceController;
use app\modules\v1\controllers\V3InvoiceTypeController;
use app\modules\v1\controllers\V3MoneyEventController;

class Permissions
{

    ///
    /// Roles
    ///

    const roleGuest = 'roleGuest';
    const roleMaster = 'roleMaster';
    const roleEdush = 'roleEdush';
    const roleBuhMain = 'roleBuhMain';
    const roleBuh = 'roleBuh';
    const roleB2bClient = 'roleB2bClient';
    const roleSaller = 'roleSaller'; // Менеджер отдела продаж
    const roleSallerMain = 'roleSallerMain'; // Руководитель отдела продаж
    const roleOxouno = 'roleOxouno'; // Пользователь розничного магазина oxouno
    const roleTechnolog = 'roleTechnolog'; //

    const roleV3Alena = 'roleV3Alena';
    const roleV3Edush = 'roleV3Edush';
    const roleV3Larisa = 'roleV3Larisa'; // Кассир рнд
    const roleV3Anna = 'roleV3Anna'; // Кассир тгн
    const roleV3Krivinosova = 'roleV3Krivinosova'; //
    const roleV3Client = 'roleV3Client'; // Роль человека, который только выставляет счета

    const roles = [
        self::roleGuest => [
            AnxUserController::actionLogin,
            AnxUserController::actionBootstrap,
            AnxUserController::actionGetUsers,
            AnxUserController::actionB2bRegister,
            AnxUserController::actionReloadAllContacts,
        ],
        self::roleMaster => [
            self::pageTestApi,
            self::roleEdush,
            self::taskMaster,
            self::taskReferenceAccess,
            self::taskReferenceB2BAccess,
            self::taskB2BUser,
            self::taskTest,
            self::taskSalesClientsAccess,
            self::taskSalesClientsWrite,
            self::taskOxounoApi,
            self::taskSalesStatisticsB2BAccess,
            self::taskSalesStatisticsB2BWrite,
            ///
            AnxUserController::postCreateUser,
            BaseController::actionPostTestData,
            SlsOrgController::actionDeleteOrg,
            RefProductPrintController::actionGetWithoutOxouno,
        ],
        self::roleEdush => [
            self::taskRegPaysPageAccess,
            self::taskRegPaysInvoiceManage,
            self::taskReferenceAccess,
            self::taskReportProduction,
            self::taskSalesClientsAccess,
            SlsMessageStateController::getMessagesForOtherManagers,
            self::taskSalesStatisticsB2BAccess,
        ],
        self::roleBuhMain => [
            self::taskRegPaysPageAccess,
            self::taskRegPaysInvoiceManage,
            self::taskBuh,
            self::taskReferenceAccess,
        ],
        self::roleBuh => [
            self::taskRegPaysPageAccess,
            self::taskBuh,
            self::taskReferenceAccess,
        ],
        self::roleB2bClient => [
            self::taskB2BUser,
        ],
        self::roleSaller => [
            self::taskSalesClientsAccess,
            SlsMessageController::actionSendFromManager,
            self::taskSalesStatisticsB2BAccess,
        ],
        self::roleSallerMain => [
            self::taskSalesClientsAccess,
            self::taskSalesClientsWrite,
            SlsMessageStateController::getMessagesForOtherManagers,
            self::taskSalesStatisticsB2BAccess,
        ],
        self::roleOxouno => [
            self::taskOxounoApi,
        ],
        self::roleTechnolog => [
            self::taskReportProduction,
        ],


        // v3

        self::roleV3Alena => [
            self::taskV3Box,
            self::taskV3RegPays,
            self::taskV3Preferences,
        ],
        self::roleV3Edush => [
            self::taskV3Invoices,
            self::taskV3RegPays,
        ],
        self::roleV3Larisa => [
            self::taskV3Invoices,
            self::taskV3Box
        ],
        self::roleV3Anna => [
            self::taskV3Invoices,
            self::taskV3Box
        ],
        self::roleV3Krivinosova => [
            self::taskV3Invoices,
            self::taskV3Preferences,
            self::taskV3RegPays,
        ],
        self::roleV3Client => [
            self::taskV3Invoices,
        ],

    ];

    ///
    /// Pages
    ///

    const pageRegPays = 'pageRegPays';

    const pageTestApi = 'pageTestApi';

    const pageReference = 'pageReference';

    const pageReferenceB2B = 'pageReferenceB2B';

    const pageSalesClients = 'pageSalesClients';

    const pageSalesStatisticsB2B = 'pageSalesStatisticsB2B';

    // Отчеты (дашборды) по производству
    const pageReportsProduction = 'pageReportsProduction';

    const pageV3Invoices = 'pageV3Invoices';

    const pageV3RegPays = 'pageV3RegPays';

    const pageV3RegPaysExpenseReport = 'pageV3RegPaysExpenseReport';

    const pageV3Box = 'pageV3Box';

    const pageV3Preferences = 'pageV3Preferences';

    const pages = [
        self::pageRegPays => 'Реестры платежей',
        self::pageTestApi => 'Тестирование API проекта',
        self::pageReference => 'Справочник изделий',
        self::pageReferenceB2B => 'B2B',
        self::pageSalesClients => 'Отдел продаж / Клиенты',
        self::pageReportsProduction => 'Отчеты / Производство',
        self::pageV3Invoices => 'Счета',
        self::pageV3RegPays => 'Реестр платежей',
        self::pageV3Box => 'Касса',
        self::pageV3Preferences => 'Настройки',
    ];


    ///
    /// Tasks
    ///

    /**
     * Разрешения для разработчиков
     */
    const taskMaster = 'taskMaster';

    /**
     * Чтение страницы "реестр платежей"
     * и подстраниц "отклоненные счета", "исходящие платежи"
     */
    const taskRegPaysPageAccess = 'taskRegPaysRead';

    /**
     * Управление счетами на странице "реестр платежей"
     */
    const taskRegPaysInvoiceManage = 'taskRegPaysInvoiceManage';

    /**
     * Задача для роли бухгалтера
     */
    const taskBuh = 'taskBuh';

    /**
     * Доступ к справочнику на просмотр
     */
    const taskReferenceAccess = 'taskReferenceAccess';

    /**
     * Доступ к B2B 1.0
     */
    const taskReferenceB2BAccess = 'taskReferenceB2BAccess';

    /**
     * Доступ к B2B 2.0 каталогу
     */
    const taskB2BUser = 'taskB2BUser';

    /**
     * Экшены для тестирования чего-либо
     */
    const taskTest = 'taskTest';

    /**
     * Экшены для доступа к странице sales/clients
     */
    const taskSalesClientsAccess = 'taskSalesClientsAccess';

    /**
     * Экшены для изменений на странице sales/clients
     */
    const taskSalesClientsWrite = 'taskSalesClientsWrite';

    /**
     * Доступ к странице pageManagementProduction и все ее API
     */
    const taskReportProduction = 'taskReportProduction';

    /**
     * Доступ к API для магазина oxouno.ru
     */
    const taskOxounoApi = 'taskOxounoApi';

    /**
     * Экшены для доступа к странице sales/statistics-b2b
     */
    const taskSalesStatisticsB2BAccess = 'taskSalesStatisticsB2BAccess';

    /**
     * Экшены для изменений на странице sales/statistics-b2b
     */
    const taskSalesStatisticsB2BWrite = 'taskSalesStatisticsB2BWrite';

    /**
     * V3
     * Экшены для страницы /invoices
     */
    const taskV3Invoices = 'taskV3Invoices';

    /**
     * V3
     * Экшены для страницы /preferences
     */
    const taskV3Preferences = 'taskV3Preferences';

    /**
     * V3
     * Экшены для страницы /reg-pays
     */
    const taskV3RegPays = 'taskV3RegPays';

    /**
     * V3
     * Экшены для страницы /box
     */
    const taskV3Box = 'taskV3Box';

    const tasks = [

        self::taskMaster => [
            BaseController::actionGetControllers,
            RefBlankModelController::actionIndex,
        ],

        self::taskRegPaysPageAccess => [
            self::pageRegPays,

            SlsClientController::actionGetForFilters,
            SlsInvoiceController::actionGetAccept,
            SlsInvoiceController::actionGetPartPay,
            SlsInvoiceController::actionGetWait,
            SlsInvoiceController::actionGetPartPayWithStateAccept,
            SlsInvoiceController::actionGetRejectInvoices,
            SlsInvoiceController::actionGetManagers,

            SlsMoneyController::getGetOut,
            SlsMoneyController::actionGetIncom,
            SlsMoneyController::getGetUsers,
            SlsMoneyController::getGetReport,
            SlsMoneyController::actionGetBankBalance,

            SlsOrderController::getGetInwork,
            SlsOrderController::getGetSend,
            SlsOrderController::getGetPrep,

            SlsPayItemController::getGetOut,
            SlsPayItemController::getGetIn,

            SlsCurrencyController::actionGetLast,
        ],

        self::taskRegPaysInvoiceManage => [
            SlsInvoiceController::actionReject,
            SlsInvoiceController::actionRejectUndo,
            SlsInvoiceController::actionSortUp,
            SlsInvoiceController::actionReturn,
            SlsInvoiceController::actionAccept,
            SlsInvoiceController::actionCreate,
            SlsInvoiceController::actionEdit,
            SlsInvoiceController::actionUploadFile,
            SlsInvoiceController::actionDeleteFile,
            SlsInvoiceController::actionGetAttachment,
            SlsMoneyController::postEditPay,
        ],

        self::taskBuh => [
            SlsMoneyController::postMoneyOut,
        ],

        self::taskReferenceAccess => [
            self::pageReference,
            RefBlankGroupController::actionGetBaseTree,
            RefBlankGroupController::actionGet,
            RefBlankClassController::actionGet,
            RefBlankModelController::actionGet,
            RefArtBlankController::actionGet,
            RefArtBlankController::actionGetForModel,
            RefArtBlankController::actionGetClientDetail,
            RefProductPrintController::actionGet,
            RefProductPrintController::actionGetClientDetail,

        ],

        self::taskReferenceB2BAccess => [
            self::pageReferenceB2B,
            RefBlankSexController::actionGetAppBarTree,
            RefBlankClassController::actionGetClassesGroupType,
            RefArtBlankController::actionGetClientDetail,
            RefArtBlankController::actionGetByFiltersExp,
        ],

        self::taskB2BUser => [
            // Аппбар
            RefBlankSexController::actionGetAppBarTree,
            SlsMessageStateController::actionGetForContact,

            // Фильтры

            RefBlankSexController::actionGetSexTags,
            RefBlankGroupController::actionGetGroups,
            RefBlankClassController::actionGetClassesGroupType,
            RefBlankThemeController::actionGetThemes,
            RefFabricTypeController::actionGetFabricTypes,
            RefArtBlankController::actionGetByFilters,
            RefArtBlankController::actionGetByFilters2,
            CardProdController::actionGetByFilters,

            // Карта товара

            CardProdController::actionGetDetails,
            SlsItemController::actionCreateItem,
            SlsItemController::actionEditItem,
            SlsItemController::actionDeleteItem,

            // Заказы

            SlsOrderController::actionGetPrep2,
            SlsClientController::actionGetLegalEntities,
            SlsOrderController::actionCreateOrder,
            SlsOrderController::actionSendOrder,

            // Сообщения

            SlsMessageController::actionGetMessagesForClient,
            SlsMessageController::actionSendFromClient,

            // Журнал заказов
            SlsOrderController::actionGetForClient,
            SlsOrderController::actionGetDetails,
            SlsOrderController::actionDeleteOrder,

            //Главная

            SlsOrgController::actionGetForContact,

            // Карта клиента

            AnxUserController::actionGetContacts,
            SlsClientController::actionUploadDocsFromContact,
            SlsClientController::actionGetDocsForContact,
        ],

        self::taskTest => [
            TestController::actionTestCode,
            TestController::actionSendMail,
            TestController::actionSendTelegram,
            TestController::actionObjPrices,
        ],

        self::taskSalesClientsAccess => [
            self::pageSalesClients,
            SlsOrgController::actionGetOrgs,
            AnxUserController::actionGetContactsByOrgId,
            SlsClientController::actionGetLegalEntitiesByOrgId,
            AnxUserController::actionGetManagers,
            SlsClientController::actionGetOutdatedLegalEntities,
            SlsMessageController::actionGetMessagesForOrg,
            SlsMessageStateController::actionGetForManager,
        ],

        self::taskSalesClientsWrite => [
            SlsOrgController::actionAccept,
            SlsOrgController::actionReject,

            SlsClientController::actionImportLegalEntity,

            SlsOrgController::actionCreateUpdate,
            AnxUserController::actionCreateUpdateForOrg,
            SlsClientController::actionCreateUpdateForOrg,
            SlsMessageController::actionSendFromManager,

            AnxUserController::actionChangeContactStatus,
            SlsClientController::actionGetDocsForManager,
            SlsClientController::actionUploadDocsFromManager,
        ],

        self::taskReportProduction => [
            self::pageReportsProduction,
            PrStorProdController::actionGetReportStorIncomAll,
            PrStorProdController::actionGetReportStorIncomMonth,
            PrStorProdController::actionGetReportStorOutMonth,
            PrStorProdController::actionGetReportOrderOut,
        ],

        self::taskOxounoApi => [
            OxounoApiController::actionGetProductCatalog,
            OxounoApiController::actionGetStorRest,
        ],

        self::taskSalesStatisticsB2BAccess => [
            self::pageSalesStatisticsB2B,
            AnxUserController::actionGetAllContacts,
            LogEventController::actionGetEvents,
        ],

        self::taskSalesStatisticsB2BWrite => [

        ],

        self::taskV3Invoices => [
            self::pageV3Invoices,
            V3InvoiceController::actionGetPrepForClient,
            V3InvoiceTypeController::actionGetAll,
            V3InvoiceController::actionCreateEdit,
            V3InvoiceController::actionDeleteByClient,
            V3MoneyEventController::actionGetPrepForClient,
            V3InvoiceController::actionGetPartPayForClient,
            V3InvoiceController::actionGetFullPayForClient,
        ],

        self::taskV3RegPays => [
            self::pageV3RegPays,
            self::pageV3RegPaysExpenseReport,
            V3InvoiceController::actionGetPrepForAdmin,
            V3BoxController::actionGetForAdmin,
            V3MoneyEventController::actionCreateForPrepInvoice,
            V3InvoiceController::actionGetPartPayForAdmin,
            V3MoneyEventController::actionGetPrepForAdmin,
            V3MoneyEventController::actionGetPayForAdmin,
            V3InvoiceController::actionDeleteByAdmin,
            V3MoneyEventController::actionSetDel,
            V3MoneyEventController::actionGetIncomingForAdmin,
            V3MoneyEventController::actionTransfer,
            V3InvoiceController::actionCreateEdit,
            V3InvoiceController::createEditAll,
        ],

        self::taskV3Box => [
            self::pageV3Box,
            V3BoxController::actionGetForCashier,
            V3MoneyEventController::actionGetPrepForCashier,
            V3MoneyEventController::actionSetPay,
            V3MoneyEventController::actionGetPayForCashier,
            V3MoneyEventController::actionGetIncomingForCashier,
            V3MoneyEventController::actionMoneyInCreate,

        ],

        self::taskV3Preferences => [
            self::pageV3Preferences,
            V3InvoiceTypeController::actionGetAll,
            V3BoxController::actionGetForAdmin,
            V3InvoiceTypeController::actionCreateEdit,
            AnxUserController::actionGetUsersFromV3,
            V3BoxController::actionCreateEdit,
        ]
    ];


    ///////////////////////////////////////

    public static function getYiiAuthItemsArray()
    {
        $listType1 = [];
        $listType2 = [];

        // Парсить pages
        foreach (self::pages as $pageKey => $pageStr) {
            $listType2[$pageKey] = ['type' => 2];
        }

        // Парсить роли
        foreach (self::roles as $key => $val) {
            $listType1[$key] = ['type' => 1, 'children' => $val];
            // Если в родителях не роль - добавить в список type2
            foreach ($val as $child) {
                if (substr($child, 0, 4) !== 'role') {
                    $listType2[$child] = ['type' => 2];
                }
            }
        }

        // Парсить задачи
        foreach (self::tasks as $key => $children) {
            $listType2[$key] = ['type' => 2, 'children' => $children];
            foreach ($children as $child) {
                $listType2[$child] = ['type' => 2];
            }
        }

        ksort($listType2);

        return array_merge($listType1, $listType2);
    }

}