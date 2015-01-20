<?php return [
    'namespace' => 'example',
    'db' => [
        //"schema" is just another word for "database"
        'schema' => 'postgis',
        'server' => 'localhost',
        'username' => 'postgis',
        'password' => 'postgis',
        'queriesFolder' => __DIR__ . '/../db/queries'
    ],
    'security' => 'cas'
];
