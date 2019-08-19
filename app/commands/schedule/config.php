<?php

namespace app\commands\schedule;

use app\commands\schedule\tasks\CBR;
use omnilight\scheduling\Schedule;


/**
 * TODO: В кроне добаивть '* * * * * php /projects/textile-api/app/yii schedule/run --scheduleFile=/projects/textile-api/app/commands/schedule/config.php'
 * @var Schedule $schedule
 */
$schedule->call(function () {
   (new CBR())->init();
})->everyMinute();


//})->hourly();
