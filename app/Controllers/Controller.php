<?php

namespace App\Controllers;

use Slim\Container;

/**
 * Class Controller
 * @package App\Controllers
 */
abstract class Controller {

    /**
     * @var mixed
     */
    protected $view;

    /**
     * @var mixed
     */
    protected $router;

    /**
     * @var mixed
     */
    protected $flash;

    /**
     * Controller constructor.
     * @param Container $container
     */
    public function __construct(Container $container){
        $this->view = $container->view;
        $this->router = $container->router;
        $this->flash = $container->flash;
    }
}