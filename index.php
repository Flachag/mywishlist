<?php
session_start();
require_once("vendor/autoload.php");

use App\Config\Database;
use App\Controllers\HomeController;
use App\Controllers\ItemController;
use App\Controllers\ListeController;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

Database::connect();

$app = new App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();
$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . '/app/Views', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    $view->getEnvironment()->addGlobal("base_path", $container->request->getUri()->getBasePath());
    return $view;
};

$container['flash'] = function () {
    return new Messages();
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render($response, 'pages/404.twig')
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html');
    };
};

$app->get('/', HomeController::class . ':home')->setName('home');

$app->post('/create-liste', ListeController::class . ':createListe')->setName('createListe');




// ANCIENNES ROUTES
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