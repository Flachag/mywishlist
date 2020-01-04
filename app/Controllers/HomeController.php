<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends Controller {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function home(Request $request, Response $response, array $args) {
        $this->view->render($response, 'pages/home.twig', [
            "current_page" => "home",
            "flash" => $this->flash->getMessages()
        ]);
    }
}