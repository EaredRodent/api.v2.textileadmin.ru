<?php

// comment out the following two lines when deployed to production
const LOCAL_WORKSTATIONS = [
    // Danya
    ['DESKTOP-7Q5VD89', 'RABBIT'],
    // Sasha
    ['tsrz-apc', 'apc', 'dell-apc']
];

if (in_array(gethostname(), array_merge(...LOCAL_WORKSTATIONS))) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
    //xdebug_disable();
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
