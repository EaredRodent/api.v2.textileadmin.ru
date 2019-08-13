<?php


namespace app\rbac;


use app\modules\v1\controllers\AnxUserController;
use app\modules\v1\controllers\BaseController;
use app\modules\v1\controllers\FilesController;
use app\modules\v1\controllers\SlsClientController;
use app\modules\v1\controllers\SlsInvoiceController;
use app\modules\v1\controllers\SlsMoneyController;
use app\modules\v1\controllers\SlsOrderController;
use app\modules\v1\controllers\SlsPayItemController;
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

    const roles = [

        self::roleGuest => [
            AnxUserController::actionLogin,
            AnxUserController::getBootstrap,
            AnxUserController::actionIndex,
        ],
        self::roleMaster => [
            self::taskPaysRead,
            self::pageTestApi,
            self::roleEdush,
            self::taskMaster,
            ///
            AnxUserController::postCreateUser,
            BaseController::actionPostTestData,

        ],
        self::roleEdush => [
            self::taskRegPaysPageAccess,
            self::taskRegPaysInvoiceManage,
        ],
    ];

    ///
    /// Pages
    ///

    const pageRegPays = 'pageRegPays';

    const pageTestApi = 'pageTestApi';

    const pagePays = 'pagePays';

    const pages = [
        self::pageRegPays => 'Реестры платежей',
        self::pageTestApi => 'Тестирование API проекта',
        self::pagePays => 'Платежи подробно'
    ];


    ///
    /// Tasks
    ///

    /**
     * Разрешения для разработчиков
     */
    const taskMaster = 'taskMaster';

    /**
     * Чтение страницы "реестр платежей" и подстраницы "отклоненные счета"
     */
    const taskRegPaysPageAccess = 'taskRegPaysRead';

    /**
     * Управление счетами на странице "реестр платежей"
     */
    const taskRegPaysInvoiceManage = 'taskRegPaysInvoiceManage';

    /**
     * Страница платежей подробная
     */

    const taskPaysRead = 'taskPaysRead';
    const taskPaysWrite = 'taskPaysWrite';


    const tasks = [
        self::taskPaysRead => [
            self::pagePays,
            SlsMoneyController::getGetOut,
            SlsMoneyController::getGetUsers,
            SlsPayItemController::getGetOut,
        ],

        self::taskPaysWrite => [

        ],

        self::taskRegPaysPageAccess => [
            self::pageRegPays,

            SlsClientController::actionGetForFilters,
            SlsInvoiceController::actionGetAccept,
            SlsInvoiceController::actionGetPartPay,
            SlsInvoiceController::actionGetWait,
            SlsInvoiceController::actionGetPartPayWithStateAccept,
            SlsInvoiceController::actionGetRejectInvoices,

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

            FilesController::actionGetInvoiceAttachment,
        ],

        self::taskRegPaysInvoiceManage => [
            SlsInvoiceController::actionReject,
            SlsInvoiceController::actionRejectUndo,
            SlsInvoiceController::actionSortUp,
            SlsInvoiceController::actionReturn,
            SlsInvoiceController::actionAccept,

            SlsMoneyController::postEditPay,
            SlsMoneyController::postMoneyOut,
        ],

        self::taskMaster => [
            BaseController::actionGetControllers,
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