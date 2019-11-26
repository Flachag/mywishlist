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
$app->run();