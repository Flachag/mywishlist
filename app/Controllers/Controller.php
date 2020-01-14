<?php

namespace App\Controllers;

use Slim\Container;

/**
 * Class Controller
 * @package App\Controllers
 */
abstract class Controller {

    /**
     * @var mixed - Attribut permettant de gérer les vues
     */
    protected $view;

    /**
     * @var mixed - Attribut permettant de gérer les routes
     */
    protected $router;

    /**
     * @var mixed - Attribut permettant de gérer les messages flash
     */
    protected $flash;

    /**
     * Constructeur de Controller.
     * @param Container $container
     */
    public function __construct(Container $container){
        $this->view = $container->view;
        $this->router = $container->router;
        $this->flash = $container->flash;
    }
}