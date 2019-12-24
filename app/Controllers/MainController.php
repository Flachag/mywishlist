<?php


namespace App\Controllers;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MainController{
    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function render(ResponseInterface $response, $file, $data = null){
        $this->container->view->render($response, $file, $data);
    }

    public function redirect($response, $name){
        return $this->withStatus(302)->withHeader('Location', $this->router->pathFor($name));
    }

    public function __get($name) {
        return $this->container->get($name);
    }
}