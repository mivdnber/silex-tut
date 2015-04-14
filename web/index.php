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
    $taartje_query = '
        select * from taartje
        where id = :id
    ';
    $stm = $db->prepare($taartje_query);
    $stm->execute(['id' => $id]);
    $taartje = $stm->fetch();

    $participants_query = '
        select * from participant
        where taartje_id = :id
    ';
    $stm = $db->prepare($participants_query);
    $stm->execute(['id' => $id]);
    $participants = $stm->fetchAll();

    return $app->render('taartje.html', [
        'taartje' => $taartje,
        'participants' => $participants
    ]);
})->bind('taartje');

$app->post('/taartje/{id}', function(App $app, Request $r, $id) use($db) {
    if(!empty($r->request->get('name'))) {
        $query = '
            insert into participant(taartje_id, name) values (:id, :name)
        ';
        $stm = $db->prepare($query);
        $stm->execute([':id' => $id, ':name' => $r->request->get('name')]);
    }

    return $app->redirect($app->path('taartje', ['id' => $id]));
});

$app->run();
