<?php

require_once "../vendor/autoload.php";

use App\Controllers\PagesController;
use App\Config\Database as DB;

DB::connect();

$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails' => true
    ]
]);

require('../app/container.php');

$app->get('/',PagesController::class.':home')->setName('home');
$app->get('/nos-listes',PagesController::class.':getListes')->setName('listes');
$app->get('/liste',PagesController::class.':getItems')->setName('items');

$app->get('/creer-liste',PagesController::class.':getListeCreate')->setName('listeCreate');
$app->get('/modifier-liste',PagesController::class.':getListeEditor')->setName('listeEditor');
$app->post('/creer-liste',PagesController::class.':postListe')->setName('listeSend');
$app->post('/modifier-liste',PagesController::class.':postListe')->setName('listeSend');

$app->get('/creer-item',PagesController::class.':getItemCreate')->setName('itemCreate');
$app->get('/modifier-item',PagesController::class.':getItemEditor')->setName('itemEditor');
$app->post('/creer-item',PagesController::class.':postItem')->setName('itemSend');
$app->post('/modifier-item',PagesController::class.':postItem')->setName('itemSend');
$app->get('/supprimer-item',PagesController::class.':deleteItem')->setName('itemDelete');


$app->run();