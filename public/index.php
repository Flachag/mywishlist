<?php

use App\Controllers\PagesController;

require_once "../vendor/autoload.php";

$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails' => true
    ]
]);

require('../app/container.php');

$app->get('/',PagesController::class.':home')->setName('home');
$app->get('/contact',PagesController::class.':getContact')->setName('contact');
$app->post('/contact',PagesController::class.':postContact');

$app->run();