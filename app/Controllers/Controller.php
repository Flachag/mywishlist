<?php


namespace App\Controllers;


use Psr\Http\Message\ResponseInterface;

class Controller{
    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function render(ResponseInterface $response, $file){
        $this->container->view->render($response, $file);
    }

    public function redirect($response, $name){
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor($name));
    }

    public function __get($name) {
        return $this->container->get($name);
    }
}