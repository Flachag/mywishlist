<?php
require_once "../vendor/autoload.php";

use App\Controllers\HomeController;
use App\Controllers\ListeController;
use App\Controllers\ItemController;
use App\Controllers\ReservationController;
use App\Config\Database as DB;

DB::connect();
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
require('../app/container.php');

$app->get('/', HomeController::class . ':home')->setName('home');

$app->get('/nos-listes', ListeController::class . ':getListes')->setName('listes');

$app->get('/liste', ItemController::class . ':getItems')->setName('items');
$app->get('/creer-liste', ListeController::class . ':getListeCreate')->setName('listeCreate');
$app->get('/modifier-liste', ListeController::class . ':getListeEditor')->setName('listeEditor');
$app->post('/creer-liste', ListeController::class . ':postListe')->setName('listeSend');
$app->post('/modifier-liste', ListeController::class . ':postListe')->setName('listeSend');

$app->get('/creer-item', ItemController::class . ':getItemCreate')->setName('itemCreate');
$app->get('/modifier-item', ItemController::class . ':getItemEditor')->setName('itemEditor');
$app->post('/creer-item', ItemController::class . ':postItem')->setName('itemSend');
$app->post('/modifier-item', ItemController::class . ':postItem')->setName('itemSend');
$app->get('/supprimer-item', ItemController::class . ':deleteItem')->setName('itemDelete');

$app->post('/partager-liste', ListeController::class . ':shareListe')->setName('listeShare');

$app->run();