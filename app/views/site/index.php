<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';


$className = 'app\modules\v1\controllers\SlsInvoiceController';
$refl = new ReflectionClass($className);

$constants = $refl->getConstants();
$actions = $refl->getMethods();



?>
<div class="site-index">
    <div class="body-content">

        <div class="row">
            <?php var_dump($actions) ?>
        </div>

        <div class="row">
            <pre><?php echo str_replace('\/', '/', json_encode(\app\rbac\Permissions::getYiiAuthItemsArray(), JSON_PRETTY_PRINT)) ?></pre>
        </div>

    </div>
</div>
