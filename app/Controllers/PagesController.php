<?php

namespace App\Controllers;

use App\models\Item;
use App\models\Liste;
use App\Models\Reservation;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PagesController extends MainController
{

    public function home(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }

    public function getListes(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/listes.twig', ["current_page" => "voir_listes", "listes" => Liste::all()]);
    }

    public function getItems(RequestInterface $request, ResponseInterface $response)
    {
        $url =  str_replace("?", "=", $request->getUri()->getQuery());
        $get = explode("=", $url);
        $liste = Liste::where('token', $get[1]);

        if ($get[0] == "token" && sizeof($get)==2 && $liste->count()==1) {
            $liste = $liste->first();
            $items = $liste->items;

            $this->render($response, 'pages/items.twig', ["current_page" => "voir_objets",
                "items" => $items,
                "liste" => $liste]);
        } elseif ($get[0]=="token" && $get[2]=="item" && sizeof($get)==4 && $liste->count()==1){
            $items = $liste->first()->items;
            foreach ($items as $item){
                if($item->id == $get[3]){
                    $this->render($response, 'pages/item.twig', ["current_page" => "item",
                                                                        "item" => $item,
                                                                        "liste" => $liste->first()]);
                }
            }
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    public function getListeCreate(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/listeCreate.twig', ["current_page" => "listeCreate", "post" => $_POST]);
    }

    public function getListeEditor(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        $liste = Liste::where('token', $get[1]);

        if ($liste->count() == 1) {
            $liste = $liste->first();
            $this->render($response, 'pages/listeEditor.twig', ["current_page" => "listeEditor", "post" => $_POST, "liste" => $liste]);
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    //pb update par save
    public function postListe(RequestInterface $request, ResponseInterface $response)
    {
        $liste = new Liste();
        $get = explode("=", $request->getUri()->getQuery());
        if (isset($get[1])) {
            $liste = Liste::where('token', $get[1])
                ->update(['titre' => strip_tags($_POST['titre']),
                          'description' => strip_tags($_POST['description']),
                          'expiration' => strip_tags($_POST['expiration'])]);
        } else {
            $liste = new Liste();
            $lastId = Liste::all()->count();
            $liste->user_id = $lastId + 1;
            $liste->token = "nosecure" . ($lastId + 1);
            $liste->titre = strip_tags($_POST['titre']);
            $liste->description = strip_tags($_POST['description']);
            $liste->expiration = strip_tags($_POST['expiration']);
            $liste->save();
        }

        //$this->redirect($response, 'home');
        $this->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }

    public function getItem(RequestInterface $request, ResponseInterface $response){
        $get = explode("=", $request->getUri()->getQuery());
        $this->render($response, 'pages/items.twig', ['current_page' => "item"]);
    }
}