<?php

namespace App\Controllers;

use App\Models\Liste;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends CookiesController {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function home(Request $request, Response $response, array $args) {
        $this->loadCookiesFromRequest($request);
        $listes = Liste::whereIn('token_edit', $this->getCreationTokens())->get();
        $this->view->render($response, 'pages/home.twig', [
            "current_page" => "home",
            "flash" => $this->flash->getMessages(),
            "listes" => $listes
        ]);
    }

    public function login(Request $request, Response $response, array $args) {
        $this->loadCookiesFromRequest($request);
        $this->view->render($response, 'pages/login.twig', [
            "current_page" => "login",
            "flash" => $this->flash->getMessages(),
        ]);
    }

}