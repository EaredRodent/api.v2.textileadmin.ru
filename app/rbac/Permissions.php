<?php


namespace app\rbac;


use app\modules\v1\controllers\AnxUserController;
use app\modules\v1\controllers\BaseController;
use app\modules\v1\controllers\FilesController;
use app\modules\v1\controllers\PrStorProdController;
use app\modules\v1\controllers\RefArtBlankController;
use app\modules\v1\controllers\RefBlankClassController;
use app\modules\v1\controllers\RefBlankGroupController;
use app\modules\v1\controllers\RefBlankModelController;
use app\modules\v1\controllers\RefBlankSexController;
use app\modules\v1\controllers\RefBlankThemeController;
use app\modules\v1\controllers\RefFabricTypeController;
use app\modules\v1\controllers\RefProdPrintController;
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
use app\modules\v1\models\ref\RefBlankClass;
use app\modules\v1\models\ref\RefBlankGroup;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefProdPrint;
use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsMoney;

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
            self::taskReferenceB2Bv2Access,
            self::taskReferenceB2Bv2Write,
            self::taskTest,
            self::taskSalesClientsAccess,
            self::taskSalesClientsWrite,
            ///
            AnxUserController::postCreateUser,
            BaseController::actionPostTestData,
            self::taskMessagesB2B,
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
            self::taskReferenceB2Bv2Access,
            self::taskMessagesB2B,
            self::taskReferenceB2Bv2Write,
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
    const taskReferenceB2Bv2Access = 'taskReferenceB2Bv2Access';

    /**
     * Доступ к B2B 2.0 каталогу (запись)
     */
    const taskReferenceB2Bv2Write = 'taskReferenceB2Bv2Write';

    /**
     * Доступ к чату в B2B
     */
    const taskMessagesB2B = 'taskMessagesB2B';

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

        self::taskReferenceB2Bv2Access => [
            //app-bar
            RefBlankSexController::actionGetAppBarTree,

            // filters

            RefBlankSexController::actionGetSexTags,
            RefBlankGroupController::actionGetGroups,
            RefBlankClassController::actionGetClassesGroupType,
            RefBlankThemeController::actionGetThemes,
            RefFabricTypeController::actionGetFabricTypes,


            // filters on action

            RefArtBlankController::actionGetByFilters,

            // viewer

            RefArtBlankController::actionGetClientDetail,

            // orders

            SlsOrderController::actionGetPrep2,
            SlsClientController::actionGetLegalEntities,
        ],

        self::taskReferenceB2Bv2Write => [
            SlsOrderController::actionCreateOrder,
            SlsItemController::actionCreateItem,
        ],

        self::taskMessagesB2B => [
            SlsMessageController::actionGetMessagesForClient,
            SlsMessageController::actionSendFromClient,
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