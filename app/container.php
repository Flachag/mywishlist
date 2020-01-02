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

function afficherImg($name){
    $name = '<td><img src="img/'.$name;
    echo $name.'" height="50"/></td>';
}

