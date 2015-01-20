<?php
$app = require_once __DIR__ . '/../app/app.php';

use geography\util\GeoJSON;

$app->get('/', function() use($app) {
    return $app->render('home.html');
});

$app->get('/map', function() use($app) {
    return $app->render('fullscreen-map.html');
});

$app->get('/map/data.json', function() use($app) {
    $data = [
        [
            'name' => 'Korenmarkt',
            'geometry' => 'POINT(3.7219780683517456 51.05467929302128)'
        ],
        [
            'name' => 'Belfort',
            'geometry' => 'POINT(3.724912405014038 51.05363549181019)'
        ]
    ];
    return $app->json(GeoJSON::features($data, ['geometry' => 'wkt']));
});

$app->get('/secure', function() use($app) {
    return 'yay!';
})->secure('ROLE_USER');

$app->run();
