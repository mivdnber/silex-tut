<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ERROR | E_WARNING | E_NOTICE);
    return [
    'namespace' => 'tvdw',
    'db' => [
        //"schema" is just another word for "database"
        'database' => 'tvdw',
        'server' => 'localhost',
        'username' => 'tvdw',
        'password' => 'tvdw',
        'queriesFolder' => __DIR__ . '/../db/queries'
    ]
];
