<?php


namespace App\Controllers;


use Slim\Container;

abstract class MainController{
    protected $view;

    public function __construct(Container $container){
        $this->view = $container->view;
    }
}