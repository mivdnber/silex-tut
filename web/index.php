<?php
$app = require_once __DIR__ . '/../app/app.php';

use geography\webapp\Application as App;
use geography\db\Connection as Db;
use Symfony\Component\HttpFoundation\Request;

$db = new PDO('pgsql:host=127.0.0.1;dbname=tvdw;user=tvdw;password=tvdw');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
})->bind('home');

$app->get('/taartje/{id}', function(App $app, $id) use($db) {
    // Todo
})->bind('taartje');

$app->run();
