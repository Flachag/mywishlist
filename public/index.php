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

$app->get('/liste/{no}[/item/{id}]', ItemController::class . ':getItem')->setName('item');
$app->get('/liste/{no}/token/{token}[/item/{id}]', ItemController::class . ':getItem')->setName('itemsEdit');

$app->get('/gestion-liste[/token/{token}]', ListeController::class . ':getListeManage')->setName('listeManage');
$app->post('/gestion-liste[/token/{token}]', ListeController::class . ':postListe')->setName('listeSend');

$app->get('/gestion-item/token/{token}[/item/{id}]', ItemController::class . ':getItemManage')->setName('itemManage');
$app->post('/gestion-item/token/{token}[/item/{id}]', ItemController::class . ':postItem')->setName('itemSend');

$app->get('/supprimer-item/item/{id}', ItemController::class . ':deleteItem')->setName('itemDelete');

$app->post('/partager-liste', ListeController::class . ':shareListe')->setName('listeShare');

$app->get('/reservation', ReservationController::class . ':getReservation')->setName('book');
$app->run();