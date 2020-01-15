<?php

namespace App\Controllers;

use App\Models\Liste;
use App\Models\Utilisateur;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends CookiesController {

    /**
     * Méthode qui redirige vers la page home
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function home(Request $request, Response $response, array $args) {
        $this->loadCookiesFromRequest($request);
        $listes = Liste::whereIn('token_edit', $this->getCreationTokens())->orderby('expiration', 'ASC')->get();
        $publiques = Liste::where('public', true)->where('expiration', '>=', date("Y-m-d"))->orderby('expiration', 'ASC')->get();

        $this->view->render($response, 'pages/home.twig', [
            "current_page" => "home",
            "flash" => $this->flash->getMessages(),
            "listes" => $listes,
            "publiques" => $publiques,
            "auteur" => null
        ]);
    }
}