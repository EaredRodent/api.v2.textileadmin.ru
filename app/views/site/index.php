<?php

/* @var $this yii\web\View */

use app\modules\v1\classes\BaseClassTemp;

$this->title = 'My Yii Application';


$className = 'app\modules\v1\controllers\SlsInvoiceController';
$refl = new ReflectionClass($className);

$constants = $refl->getConstants();
$actions = $refl->getMethods();


$api = BaseClassTemp::getApi2();
$apiJson = str_replace('\/', '/', json_encode($api, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE))

?>
<div class="site-index">
    <div class="body-content">


        <div class="row">
            <pre><?=Yii::$app->security->generateRandomString(16)?></pre>
            <pre><?php echo $apiJson ?></pre>
        </div>

<!--        <div class="row">-->
<!--            --><?php //var_dump($actions) ?>
<!--        </div>-->
    </div>
</div>
