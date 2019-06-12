<?php
return [
    'ROLE Admin' => [
        'type' => 1,
        'children' => [
            'ROLE Moderator',
        ],
    ],
    'ROLE Master' => [
        'type' => 1,
        'children' => [
            'ROLE Admin',
            'GET /v1/auth-item/browse-roles',
            'POST /v1/auth-item/create-role',
            'POST /v1/auth-item/create-permission',
            'POST /v1/auth-item/extend-role-by-role',
            'POST /v1/auth-item/extend-role-by-permission',
            'POST /v1/auth-item/extend-permission-by-permission',
            'POST /v1/auth-item/revoke-child',
            'POST /v1/auth-item/remove',
            '/management/reg-pays',
            '/base/roles',
            'GET /v1/sls-client/get-for-filters',
            'GET /v1/sls-invoice/get-accept',
            'GET /v1/sls-invoice/get-part-pay',
            'GET /v1/sls-invoice/get-wait',
            'GET /v1/sls-money/get-out',
            'GET /v1/sls-money/get-incom',
            'GET /v1/sls-order/get-inwork',
            'GET /v1/sls-order/get-send',
            'GET /v1/sls-order/get-prep',
            '/files',
            'POST /v1/sls-invoice/reject',
            'POST /v1/sls-invoice/sort-up',
            'POST /v1/sls-invoice/return',
            'POST /v1/sls-invoice/accept',
            'GET /v1/sls-invoice/get-part-pay-with-state-accept',
            'POST /v1/sls-money/money-out',
            'GET /v1/sls-pay-item/get-out',
            'GET /v1/sls-pay-item/get-in',
            'GET /v1/sls-currency/get-last',
            '/management/pays',
            'GET /v1/sls-money/get-users',
        ],
    ],
    'ROLE Moderator' => [
        'type' => 1,
        'children' => [
            'ROLE User',
        ],
    ],
    'ROLE User' => [
        'type' => 1,
    ],
    'ROLE Guest' => [
        'type' => 1,
        'children' => [
            'POST /v1/anx-user/login',
            'GET /v1/anx-user/bootstrap',
        ],
    ],
    'POST /v1/anx-user/login' => [
        'type' => 2,
    ],
    'GET /v1/anx-user/bootstrap' => [
        'type' => 2,
    ],
    'GET /v1/auth-item/browse-roles' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/create-role' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/create-permission' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/extend-role-by-role' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/extend-role-by-permission' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/extend-permission-by-permission' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/revoke-child' => [
        'type' => 2,
    ],
    'POST /v1/auth-item/remove' => [
        'type' => 2,
    ],
    '/management/reg-pays' => [
        'type' => 2,
    ],
    '/base/roles' => [
        'type' => 2,
    ],
    'GET /v1/sls-client/get-for-filters' => [
        'type' => 2,
    ],
    'GET /v1/sls-invoice/get-accept' => [
        'type' => 2,
    ],
    'GET /v1/sls-invoice/get-part-pay' => [
        'type' => 2,
    ],
    'GET /v1/sls-invoice/get-wait' => [
        'type' => 2,
    ],
    'GET /v1/sls-money/get-out' => [
        'type' => 2,
    ],
    'GET /v1/sls-money/get-incom' => [
        'type' => 2,
    ],
    'GET /v1/sls-order/get-inwork' => [
        'type' => 2,
    ],
    'GET /v1/sls-order/get-send' => [
        'type' => 2,
    ],
    'GET /v1/sls-order/get-prep' => [
        'type' => 2,
    ],
    '/files' => [
        'type' => 2,
    ],
    'POST /v1/amfiles-directory/create' => [
        'type' => 2,
    ],
    'POST /v1/amfiles-file/update' => [
        'type' => 2,
    ],
    'POST /v1/sls-invoice/reject' => [
        'type' => 2,
    ],
    'POST /v1/sls-invoice/sort-up' => [
        'type' => 2,
    ],
    'POST /v1/sls-invoice/return' => [
        'type' => 2,
    ],
    'POST /v1/sls-invoice/accept' => [
        'type' => 2,
    ],
    'GET /v1/sls-invoice/get-part-pay-with-state-accept' => [
        'type' => 2,
    ],
    'POST /v1/sls-money/money-out' => [
        'type' => 2,
    ],
    'GET /v1/sls-pay-item/get-out' => [
        'type' => 2,
    ],
    'GET /v1/sls-pay-item/get-in' => [
        'type' => 2,
    ],
    'GET /v1/sls-currency/get-last' => [
        'type' => 2,
    ],
    '/management/pays' => [
        'type' => 2,
    ],
    'GET /v1/sls-money/get-users' => [
        'type' => 2,
    ],
];
