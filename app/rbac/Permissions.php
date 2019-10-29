<?php


namespace app\rbac;


use app\modules\v1\controllers\AnxUserController;
use app\modules\v1\controllers\BaseController;
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
use app\modules\v1\controllers\SlsMoneyController;
use app\modules\v1\controllers\SlsOrderController;
use app\modules\v1\controllers\SlsOrgController;
use app\modules\v1\controllers\SlsPayItemController;
use app\modules\v1\controllers\TestController;

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


    const roles = [
        self::roleGuest => [
            AnxUserController::actionLogin,
            AnxUserController::actionBootstrap,
            AnxUserController::actionIndex,
            AnxUserController::actionB2bRegister,
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
            ///
            AnxUserController::postCreateUser,
            BaseController::actionPostTestData,
            SlsOrgController::actionDeleteOrg,
        ],
        self::roleEdush => [
            self::taskRegPaysPageAccess,
            self::taskRegPaysInvoiceManage,
            self::taskReferenceAccess,
            self::taskReportProduction,
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
        ],
        self::roleSallerMain => [
            self::taskSalesClientsAccess,
            self::taskSalesClientsWrite,
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

    // Отчеты (дашборды) по производству
    const pageManagamentProduction = 'pageManagamentProduction';

    const pages = [
        self::pageRegPays => 'Реестры платежей',
        self::pageTestApi => 'Тестирование API проекта',
        self::pageReference => 'Справочник изделий',
        self::pageReferenceB2B => 'B2B',
        self::pageSalesClients => 'Отдел продаж / Клиенты',
        self::pageManagamentProduction => 'Производство',
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
     * Доступ к странице pageManagamentProduction и все ее API
     */
    const taskReportProduction = 'taskReportProduction';

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

            // Фильтры

            RefBlankSexController::actionGetSexTags,
            RefBlankGroupController::actionGetGroups,
            RefBlankClassController::actionGetClassesGroupType,
            RefBlankThemeController::actionGetThemes,
            RefFabricTypeController::actionGetFabricTypes,
            RefArtBlankController::actionGetByFilters,
            RefArtBlankController::actionGetByFilters2,

            // Карта товара

            RefArtBlankController::actionGetClientDetail,
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

            //Главная

            SlsOrgController::actionGetForContact,

            // Карта клиента

            AnxUserController::actionGetContacts,

        ],

        self::taskTest => [

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
            SlsMessageController::actionGetMessagesForOrg
        ],

        self::taskSalesClientsWrite => [
            SlsOrgController::actionAccept,
            SlsOrgController::actionReject,

            SlsClientController::actionImportLegalEntity,

            SlsOrgController::actionCreateUpdate,
            AnxUserController::actionCreateUpdateForOrg,
            SlsClientController::actionCreateUpdateForOrg,
            SlsMessageController::actionSendFromManager,
        ],

        self::taskReportProduction => [
            self::pageManagamentProduction,
            PrStorProdController::actionGetReportStorIncomAll,
            PrStorProdController::actionGetReportStorIncomMonth,
            PrStorProdController::actionGetReportStorOutMonth,
        ],
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