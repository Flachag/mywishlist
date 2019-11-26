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

$app->post('/creer-liste',PagesController::class.':postListeCreate')->setName('listeCreate');
$app->run();