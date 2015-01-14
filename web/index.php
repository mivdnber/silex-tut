<?php
$app = require_once __DIR__ . '/../app/app.php';

use example\Greeter;

$app->get('/', function() use($app) {
    return $app->render('greeting.html', [
        'greeting' => Greeter::greet()
    ]);
});

$app->get('/hello/{name}', function($name) use($app) {
    return $app->render('greeting.html', [
        'greeting' => Greeter::greet($name)
    ]);
});

$app->run();
