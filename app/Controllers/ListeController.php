<?php


namespace App\Controllers;


use App\models\Item;
use App\models\Liste;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ListeController extends MainController
{

    public function getListes(RequestInterface $request, ResponseInterface $response)
    {
        $this->view->render($response, 'pages/listes.twig', ["current_page" => "voir_listes", "listes" => Liste::all()]);
        $items = Item::all();
        foreach ($items as $item) {
            if (!empty($item->img) && !str_contains($item->img, "/assets/img/")) {
                $headers = @get_headers($item->img);
                if ($headers == false) {
                    $img = "/assets/img/" . $item->img;
                    $item->img = $img;
                    $item->save();
                }
            }
        }
    }

    public function getListeManage(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("/token/", $request->getUri());
        $liste = null;
        if (isset($get[1])) {
            $liste = Liste::where('token', $get[1]);
            if ($liste->count() == 1) {
                $liste = $liste->first();
                $this->view->render($response, 'pages/listeManage.twig', ["current_page" => "listeManage", "liste" => $liste]);
            } else {
                $this->view->render($response, 'pages/404.twig', ["current_page" => "404"]);
            }
        } else {
            $this->view->render($response, 'pages/listeManage.twig', ["current_page" => "listeManage", "liste" => $liste]);
        }
    }

    public function createList(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $titre = filter_var($request->getParsedBodyParam('titre'), FILTER_SANITIZE_STRING);
        $description = filter_var($request->getParsedBodyParam('description'), FILTER_SANITIZE_STRING);
        $expiration = $request->getParsedBodyParam('expiration');

        if (new DateTime() > new DateTime($expiration)) throw new Exception("La date d'expiration ne peut être déjà passée.");

        $liste = new Liste();
        $lastId = Liste::all()->count();
        $liste->user_id = $lastId + 1;
        $liste->token_edit = "nosecure" . ($lastId + 1);
        $liste->token =
        $liste->titre = $titre;
        $liste->description = $description;
        $liste->expiration = $expiration;
        $liste->save();
        //$this->redirect($response, 'home');
        $this->view->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }
}