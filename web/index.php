<?php
$app = require_once __DIR__ . '/../app/app.php';

use geography\webapp\Application as App;

$db = new PDO('pgsql:host=127.0.0.1;dbname=tvdw;user=tvdw;password=tvdw');

$app->get('/', function(App $app) use($db) {
    $query = '
        select * from taartje
        where date >= now()::date
        order by date asc
    ';
    $stm = $db->prepare($query);
    $stm->execute();
    $taartjes = $stm->fetchAll();

    return $app->render('home.html', [
        'taartjes' => $taartjes
    ]);
});

$app->run();
