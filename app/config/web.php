<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\V1Mod',
        ],
    ],

    'runtimePath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'runtime-web',

    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'yQdIbN-4kT5Z_nTKa_0TUtEJY1rF3_0e',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'on beforeSend' => function ($event) {
//                $logger = Yii::getLogger();
//
//                $dbCountQuery = $logger->getDbProfiling()[0];
//                $dbTime = round($logger->getDbProfiling()[1], 3);
//                $appTime = round($logger->elapsedTime, 3);
//                $appMemory = number_format(memory_get_peak_usage(), 0, '', ' ');
//
//                $headers = Yii::$app->response->headers;
//
//                $headers->add('Log-Dbcount', $dbCountQuery);
//                $headers->add('Log-Dbtime', $dbTime);
//                $headers->add('Log-Apptime', $appTime);
//                $headers->add('Log-Appmemory', $appMemory);
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\AnxUser',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => ['/'], // это return $this->goHome();
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
//                [
//                    'class' => 'yii\log\FileTarget',
//                    'logFile' => '@runtime/logs/profile.log',
//                    'logVars' => [],
//                    'levels' => ['profile'],
//                    'categories' => ['yii\db\Command::query'],
//                    'prefix' => function($message) {
//                        return '';
//                    }
//                ]
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['roleGuest'],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,

            'rules' => [

                'v1/files/public/<dir:[\w-]+>/<name:[\w-.()\s]+>' => 'v1/files/public',
                'v1/files/get/<key:[\w-]+>/<dir:[\w-]+>/<name:[\w-.()\s]+>' => 'v1/files/get',

                'sales/orders/order/<id:\d+>' => 'sales/orders/order',
                'api/excel-price/<key:[A-Za-z0-9_-]+>' => 'api/excel-price',
                'get-file/<key:[A-Za-z0-9_-]+>' => 'file-storage/amfiles-def/api-shared-download',
                'PUT,PATCH <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/update',
                'DELETE <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/delete',
                'GET,HEAD <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/view',
                'POST <module:[\w-]+>/<controller:[\w-]+>' => '<module>/<controller>/create',
                'GET,HEAD <module:[\w-]+>/<controller:[\w-]+>' => '<module>/<controller>/index',
                '<module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/options',
                '<module:[\w-]+>/<controller:[\w-]+>' => '<module>/<controller>/options',
                'GET,HEAD,POST <module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/<action>',   // custom action
                '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/options',    // custom action
                'api/<module:[A-Za-z0-9_-]+>/<cmd:[A-Za-z0-9_-]+>' => 'api',

                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => ['api'],
                    'extraPatterns' => [
                        'OPTIONS <action:.*>' => 'options',
                    ],
                ],
            ],
        ],
    ],

    'on afterRequest' => function($event) {
        //...

    },
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
