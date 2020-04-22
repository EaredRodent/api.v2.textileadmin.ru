<?php

switch (CURRENT_API_URL) {
    case 'http://api.textileadmin.loc':
        $configDb['class'] = 'yii\db\Connection';
        $configDb['dsn'] = 'mysql:host=localhost:3309;dbname=textile';
        $configDb['username'] = 'root';
        $configDb['password'] = '';
        $configDb['charset'] = 'utf8mb4';
        break;
    case 'https://api.b2b.oxouno.ru':
        $configDb['class'] = 'yii\db\Connection';
        $configDb['dsn'] = 'mysql:host=localhost;dbname=textile';
        $configDb['username'] = 'dbuser';
        $configDb['password'] = 'cnbdtyrbyu';
        $configDb['charset'] = 'utf8mb4';
        break;
    case 'https://dev.api.b2b.oxouno.ru':
        $configDb['class'] = 'yii\db\Connection';
        $configDb['dsn'] = 'mysql:host=localhost:3309;dbname=textile';
        $configDb['username'] = 'dbuser';
        $configDb['password'] = 'kojure901zUq';
        $configDb['charset'] = 'utf8mb4';
        break;
}

// Schema cache options (for production environment)
if (YII_ENV_PROD) {
    $configDb['enableSchemaCache'] = true;
    $configDb['schemaCacheDuration'] = 60;
    $configDb['schemaCache'] = 'cache';
}

return $configDb;
