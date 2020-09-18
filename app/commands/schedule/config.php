<?php

namespace app\commands\schedule;

use app\commands\schedule\tasks\CacheB2B;
use app\commands\schedule\tasks\CBR;
use app\commands\schedule\tasks\OrderCleaner;
use omnilight\scheduling\Schedule;


/**
 * TODO: В кроне добаивть '* * * * * php /projects/textile-api/app/yii schedule/run --scheduleFile=/projects/textile-api/app/commands/schedule/config.php'
 * Тестирование для винды 'yii schedule/run --scheduleFile=commands/schedule/config.php'
 * @var Schedule $schedule
 */
$schedule->call(function () {
   (new CBR())->init();
})->everyFiveMinutes();

$schedule->call(function () {
   (new OrderCleaner())->init();
})->everyMinute();

$schedule->call(function () {
   (new CacheB2B())->init();
})->everyMinute();