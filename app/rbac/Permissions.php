<?php


namespace app\rbac;


use app\modules\v1\controllers\AnxUserController;
use app\modules\v1\controllers\SlsClientController;
use app\modules\v1\controllers\SlsInvoiceController;
use app\modules\v1\controllers\SlsMoneyController;
use app\modules\v1\controllers\SlsOrderController;
use app\modules\v1\controllers\SlsPayItemController;
use app\modules\v1\models\sls\SlsInvoice;
use app\modules\v1\models\sls\SlsMoney;

class Permissions
{

    /// Roles

    const roleGuest = 'roleGuest';
    const roleMaster = 'roleMaster';
    const roleEdush = 'roleEdush';

    const roles = [
        self::roleGuest => [
            AnxUserController::postLogin,
            AnxUserController::getBootstrap,
            AnxUserController::getUsers,
        ],
        self::roleMaster => [
            AnxUserController::postCreateUser,
            self::roleEdush,
        ],
        self::roleEdush => [
            self::taskRegPays,
        ],
    ];

    /// Pages

    const pageRegPays = 'pageRegPays';

    const pages = [
        self::pageRegPays => 'Реестры платежей'
    ];


    /// Tasks

    const taskRegPays = 'taskRegPays';

    const tasks = [
        self::taskRegPays => [
            self::pageRegPays,
            SlsClientController::getGetForFilters,
            SlsInvoiceController::getGetAccept,
            SlsInvoiceController::getGetPartPay,
            SlsInvoiceController::getGetWait,
            SlsInvoiceController::getGetPartPayWithStateAccept,
            SlsInvoiceController::postReject,
            SlsInvoiceController::postSortUp,
            SlsInvoiceController::postReturn,
            SlsInvoiceController::postAccept,
            SlsMoneyController::getGetOut,
            SlsMoneyController::getGetIncom,
            SlsMoneyController::getGetUsers,
            SlsMoneyController::getGetReport,
            SlsMoneyController::postEditPay,
            SlsMoneyController::postMoneyOut,
            SlsOrderController::getGetInwork,
            SlsOrderController::getGetSend,
            SlsOrderController::getGetPrep,
            SlsPayItemController::getGetOut,
            SlsPayItemController::getGetIn,
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