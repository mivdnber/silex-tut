<?php
$app = require_once __DIR__ . '/../app/app.php';

use geography\webapp\Application as App;

$app->get('/', function(App $app) {
    return $app->render('home.html');
});

$app->run();
