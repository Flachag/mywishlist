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
        if ($get[0] == "liste" && filter_var($get[1],FILTER_VALIDATE_INT) &&
                sizeof($get)==2 && Liste::where('no', $get[1])->count()==1){
            $this->render($response,'pages/items.twig',  ["current_page" => "voir_objets",
                                                                "items" => Item::where('liste_id',$get[1])->get(),
                                                                "num" => $get[1]]);
        } else {
            $this->render($response,'pages/404.twig', ["current_page" => "404"]);
        }
    }
}