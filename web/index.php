<?php
$app = require_once __DIR__ . '/../app/app.php';

use geography\webapp\Application as App;

$app->get('/', function(App $app) {
    // This should be in your database.
    $taartjes = [
        [
            'id' => 1,
            'date' => '2015-04-17',
            'organizer' => 'Michiel',
            'pitch' => 'Hoera, taartjes!'
        ],
        [
            'id' => 2,
            'date' => '2015-04-24',
            'organizer' => 'Berdien',
            'pitch' => 'Om nom nom'
        ],
        [
            'id' => 3,
            'date' => '2015-05-08',
            'organizer' => 'Bart',
            'pitch' => 'Deze keer ijsjes!'
        ],
        [
            'id' => 4,
            'date' => '2015-05-15',
            'organizer' => 'Tim',
            'pitch' => 'Zelf gebakken!'
        ],
    ];

    return $app->render('home.html', [
        'taartjes' => $taartjes
    ]);
});

$app->run();
