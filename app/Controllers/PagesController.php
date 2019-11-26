<?php
namespace App\Controllers;

use App\models\Item;
use App\models\Liste;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PagesController extends MainController {

    public function home(RequestInterface $request, ResponseInterface $response){
        $this->render($response,'pages/home.twig', ["current_page" => "home"]);
    }

    public function getListes(RequestInterface $request, ResponseInterface $response){
        $this->render($response,'pages/listes.twig',  ["current_page" => "voir_listes", "listes" => Liste::all()]);
    }

    public function getItems(RequestInterface $request, ResponseInterface $response){
        $get = explode("=", $request->getUri()->getQuery());
        $liste = Liste::where('token', $get[1]);

        if ($get[0] == "liste" && sizeof($get)==2 && $liste->count()==1) {
            $liste = $liste->first();
            $this->render($response, 'pages/items.twig', ["current_page" => "voir_objets",
                "items" => Item::where('liste_id', $liste->no)->get(),
                "liste" => $liste]);
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    public function getListeCreate(RequestInterface $request, ResponseInterface $response){
        $this->render($response,'pages/listeCreate.twig',  ["current_page" => "listeCreate"]);
    }
}