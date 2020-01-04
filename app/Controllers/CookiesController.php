<?php

namespace App\controllers;

use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CookiesController
 * @package App\controllers
 */
abstract class CookiesController extends Controller {

    /**
     * @var array Stock les informations de cookie sous forme de tableau
     */
    private $infos;

    /**
     * Construit un Controller et génère des infos vide de base
     * avec generateEmptyCookie()
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->infos = self::generateEmptyCookie();
    }

    /**
     * Cette méthode créer une base pour traiter les informations
     * @return array
     */
    private static function generateEmptyCookie() {
        return [
            "name" => "",
            "showRes" => false,
            "creationTokens" => []
        ];
    }

    /**
     * Cette méthode charge les cookies de l'utilisateur
     * à partir d'une requête et vérifie que cela correspond
     * à ce que l'on attends
     * @param Request $request
     */
    public function loadCookiesFromRequest(Request $request) {
        $cookies = self::getCookies($request);
        if ($cookies->has("wl_infos") && is_array(json_decode(urldecode($cookies->get("wl_infos")->getValue()), TRUE))) {
            $arr = json_decode(urldecode($cookies->get("wl_infos")->getValue()), TRUE);
            if (isset($arr['name']) && isset($arr['showRes']) && isset($arr['creationTokens']) && is_array($arr['creationTokens'])) {
                $this->infos = $arr;
            }
        }
    }

    /**
     * Cette méthode récupère les cookies à partir d'une requête
     * et renvoie une collection de Cookies
     * @param Request $request
     * @return Cookies
     */
    private static function getCookies(Request $request): Cookies {
        return Cookies::fromRequest($request);
    }

    /**
     * Créer une réponse avec un cookie contenant
     * les informations stockées
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createResponseCookie(Response $response) {
        return SetCookies::fromResponse($response)
            ->with(SetCookie::createRememberedForever("wl_infos")->withPath("/")->withValue(json_encode($this->infos)))
            ->renderIntoSetCookieHeader($response);
    }

    /**
     * Renvoie le nom stocké
     * @return String
     */
    public function getName() {
        return $this->infos['name'];
    }

    /**
     * Change le nom stocké
     * @param String $name
     */
    public function changeName(String $name) {
        $this->infos['name'] = $name;
    }

    /**
     * Renvoie le booléen stocké
     * @return String
     */
    public function getShowRes() {
        return $this->infos['showRes'];
    }

    /**
     * Change le booléen stocké
     * @param String $name
     */
    public function changeShowRes(bool $b) {
        $this->infos['showRes'] = $b;
    }

    /**
     * Retourne les tokens de création stockés
     * @return array
     */
    public function getCreationTokens() {
        return $this->infos['creationTokens'];
    }

    /**
     * Ajoute un token de creation dans le tableau stocké
     * @param String $token
     */
    public function addCreationToken(String $token) {
        if (!in_array($token, $this->infos['creationTokens'])) {
            array_push($this->infos['creationTokens'], $token);
        }
    }

    /**
     * Supprime un token de creation dans le tableau stocké
     * @param String $token
     */
    public function deleteCreationToken(String $token) {
        if (in_array($token, $this->infos['creationTokens'])) {
            unset($this->infos['creationTokens'][$token]);
        }
    }
}