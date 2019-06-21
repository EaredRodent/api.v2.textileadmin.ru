<?php

// comment out the following two lines when deployed to production
if (in_array(gethostname(), ['tsrz-apc', 'apc', 'dell-apc', 'DESKTOP-7Q5VD89'])) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
    //xdebug_disable();
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
