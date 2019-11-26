<?php
namespace App\Controllers;

use App\models\Item;
use App\models\Liste;
use App\Models\Reservation;
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
            $items =  $liste->items;

            $this->render($response, 'pages/items.twig', ["current_page" => "voir_objets",
                                                                "items" => $items,
                                                                "liste" => $liste]);
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    public function getListeCreate(RequestInterface $request, ResponseInterface $response){
        $this->render($response,'pages/listeCreate.twig',  ["current_page" => "listeCreate", "post" => $_POST]);
    }

    public function postListeCreate(RequestInterface $request, ResponseInterface $response){
        $lastId = Liste::all()->count();
        $liste = new Liste();
        $liste->user_id = $lastId + 1;
        $liste->titre = strip_tags($_POST['titre']);
        $liste->description = strip_tags($_POST['description']);
        $liste->expiration = strip_tags($_POST['expiration']);
        $liste->token = "nosecure" . ($lastId + 1);
        $liste->save();
        //$this->redirect($response, 'home');
        $this->render($response,'pages/home.twig', ["current_page" => "home"]);
    }
}