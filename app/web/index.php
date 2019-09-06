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

///
define('APP_ROOT', realpath(__DIR__ . '/../'));
$date = date('Y-m-d H:i:s', $_SERVER["REQUEST_TIME_FLOAT"]);
$url = $_SERVER['REQUEST_URI'];
$ip = $_SERVER['REMOTE_ADDR'];
$mem = number_format(
    memory_get_peak_usage(true), 0, ',', ' '
);
$dur = number_format(
    round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 3), 3, '.', ' '
);


$logger = Yii::getLogger();
$dbCountQuery = $logger->getDbProfiling()[0];
$dbTime = round($logger->getDbProfiling()[1], 3);


$dataStr = "$date | $ip | $dur | $mem | (*)$dbCountQuery | (*)$dbTime | $url \n";
file_put_contents(APP_ROOT . '/../log/time-memory.txt', $dataStr, FILE_APPEND);