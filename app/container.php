<?php
$container = $app->getContainer();

$container['view'] = function ($container) {
    $dir = dirname(__DIR__);
    $view = new \Slim\Views\Twig($dir.'/app/views', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $base =  $container["request"]->getUri()->getBasePath();
    $view->getEnvironment()->addGlobal("base_path", $base);
    return $view;
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render($response, 'pages/404.twig')
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html');
    };
};

