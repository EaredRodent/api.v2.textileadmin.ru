<?php


namespace app\rbac;


use app\modules\v1\controllers\AnxUserController;
use app\modules\v1\controllers\BaseController;
use app\modules\v1\controllers\FilesController;
use app\modules\v1\controllers\RefArtBlankController;
use app\modules\v1\controllers\RefBlankClassController;
use app\modules\v1\controllers\RefBlankGroupController;
use app\modules\v1\controllers\RefBlankModelController;
use app\modules\v1\controllers\RefBlankThemeController;
use app\modules\v1\controllers\RefFabricTypeController;
use app\modules\v1\controllers\RefProdPrintController;
use app\modules\v1\controllers\RefProductPrintController;
use app\modules\v1\controllers\SlsClientController;
use app\modules\v1\controllers\SlsCurrencyController;
use app\modules\v1\controllers\SlsInvoiceController;
use app\modules\v1\controllers\SlsMoneyController;
use app\modules\v1\controllers\SlsOrderController;
use app\modules\v1\controllers\SlsPayItemController;
use app\modules\v1\models\ref\RefBlankClass;
use app\modules\v1\models\ref\RefBlankGroup;
use app\modules\v1\models\ref\RefBlankModel;
use app\modules\v1\models\ref\RefProdPrint;
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

    const roles = [

        self::roleGuest => [
            AnxUserController::actionLogin,
            AnxUserController::getBootstrap,
            AnxUserController::actionIndex,
        ],
        self::roleMaster => [
            self::pageTestApi,
            self::roleEdush,
            self::taskMaster,
            self::taskReferenceAccess,
            ///
            AnxUserController::postCreateUser,
            BaseController::actionPostTestData,
        ],
        self::roleEdush => [
            self::taskRegPaysPageAccess,
            self::taskRegPaysInvoiceManage,
            self::taskReferenceAccess,
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

    ];

    ///
    /// Pages
    ///

    const pageRegPays = 'pageRegPays';

    const pageTestApi = 'pageTestApi';

    const pageReference = 'pageReference';

    const pages = [
        self::pageRegPays => 'Реестры платежей',
        self::pageTestApi => 'Тестирование API проекта',
        self::pageReference => 'Справочник изделий',
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

    const tasks = [

        self::taskMaster => [
            BaseController::actionGetControllers,
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
            RefBlankGroupController::actionGetSort,
            RefBlankGroupController::actionGetTree,
            RefBlankGroupController::actionGetBaseTree,
            RefBlankModelController::actionGetForClass,
            RefArtBlankController::actionGetForModel,
            ///
            RefArtBlankController::actionGetProps,
            ///
            RefBlankClassController::actionGetForGroupSex,
            RefBlankClassController::actionGetClassesExp,
            RefBlankClassController::actionGetClassesGroupType,
            RefArtBlankController::actionGetByFiltersExp,
            RefArtBlankController::actionGetAllExp,
            RefArtBlankController::actionGetClientDetail,
            //
            RefBlankThemeController::actionGetAll,
            RefFabricTypeController::actionGetAll,
            //
            RefBlankGroupController::actionGet,
            RefBlankClassController::actionGet,
            RefBlankModelController::actionGet,
            RefArtBlankController::actionGet,
            RefProductPrintController::actionGet,
            RefProductPrintController::actionGetClientDetail,

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