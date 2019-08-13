<?php

$configDb = [
    'class' => 'yii\db\Connection',
    'dsn' => YII_ENV_DEV ?
        'mysql:host=localhost:3309;dbname=textile' : 'mysql:host=localhost;dbname=textile',
    'username' => YII_ENV_DEV ?
        'root' : 'dbuser',
    'password' => YII_ENV_DEV ?
        '' : 'cnbdtyrbyu',
    'charset' => 'utf8mb4',
];

// Schema cache options (for production environment)
if (YII_ENV_PROD) {
    $configDb['enableSchemaCache'] = true;
    $configDb['schemaCacheDuration'] = 60;
    $configDb['schemaCache'] = 'cache';
}

return $configDb;