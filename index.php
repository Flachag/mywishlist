<?php
use App\Config\Database;
use App\Controllers\HomeController;
use App\Controllers\ItemController;
use App\Controllers\ListeController;
use App\Controllers\ConnectionController;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

session_start();

require_once("vendor/autoload.php");
Database::connect();

$app = new App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();
$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . '/App/Views', [
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

$app->get('/liste/{token:[a-zA-Z0-9]+}/{id:[0-9]+}', ItemController::class . ':getItem')->setName('item');
$app->post('/liste/{token:[a-zA-Z0-9]+}/book/{id:[0-9]+}', ItemController::class . ':bookItem')->setName('bookItem');

$app->get('/liste/{token:[a-zA-Z0-9]+}', ListeController::class . ':getListe')->setName('liste');
$app->get('/create-liste', ListeController::class . ':createForm')->setName('formListe');
$app->post('/create-liste', ListeController::class . ':createListe')->setName('createListe');
$app->post('/liste/{token:[a-zA-Z0-9]+}/message', ListeController::class.':addMessage')->setName('addMessage');

$app->get('/liste/{token:[a-zA-Z0-9]+}/admin/{token_edit:[a-zA-Z0-9]+}', ListeController::class . ':adminListe')->setName('formListeAdmin');

$app->post('/liste/{token:[a-zA-Z0-9]+}/edit', ListeController::class . ':manageListe')->setName('manageListe');

$app->post('/liste/{token:[a-zA-Z0-9]+}/manage/{id:[0-9]+}', ItemController::class . ':manageItem')->setName('manageItem');

$app->post('/liste/{token:[a-zA-Z0-9]+}/manageItem/', ItemController::class . ':ajoutItem')->setName('ajoutItem');
$app->post('/liste/{token:[a-zA-Z0-9]+}/manageItem/{id:[0-9]+}', ItemController::class . ':manageItem')->setName('manageItem');

$app->get('/login', HomeController::class . ':getLogin')->setName('loginPage');

$app->post('/register', ConnectionController::class.':inscription')->setName('sendInscription');
$app->get('/inscription', ConnectionController::class.':getInscription')->setName('inscriptionPage');
$app->run();