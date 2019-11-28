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
        $url = str_replace("?", "=", $request->getUri()->getQuery());
        $get = explode("=", $url);
        $liste = Liste::where('token', $get[1]);

        if ($get[0] == "token" && sizeof($get) == 2 && $liste->count() == 1) {
            $liste = $liste->first();
            $items = $liste->items;

            $this->render($response, 'pages/items.twig', ["current_page" => "voir_objets",
                "items" => $items,
                "liste" => $liste]);
        } elseif ($get[0] == "token" && $get[2] == "item" && sizeof($get) == 4 && $liste->count() == 1) {
            $items = $liste->first()->items;
            foreach ($items as $item) {
                if ($item->id == $get[3]) {
                    $find = true;
                    if (!empty($item->img) && !str_contains($item->img, "assets/img/")) {
                        $headers = @get_headers($item->img);
                        if (!isset($headers)) {
                            $img = "assets/img/" . $item->img;
                            $item->img = $img;
                            $item->save();
                        }
                    }
                    $this->render($response, 'pages/item.twig', ["current_page" => "item",
                        "item" => $item,
                        "liste" => $liste->first()]);
                }
            }
            if (!isset($find)) {
                $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
            }
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    public function getListeCreate(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/listeCreate.twig', ["current_page" => "listeCreate", "post" => $_POST]);
    }

    public function getItemCreate(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        $liste = Liste::where('token', $get[1]);
        if ($liste->count() == 1) {
            $liste = $liste->first();
            $this->render($response, 'pages/itemCreate.twig', ["current_page" => "itemCreate", "post" => $_POST, "liste" => $liste]);
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
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

    public function getItemEditor(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        $item = Item::where('id', $get[1]);

        if ($item->count() == 1) {
            $item = $item->first();
            $reservation = Reservation::where('id_item', $get[1]);
            if ($reservation->count() == 1) {
                $this->render($response, 'pages/reserved.twig', ["current_page" => "reserved"]);
            } else {
                $this->render($response, 'pages/itemEditor.twig', ["current_page" => "itemEditor", "post" => $_POST, "item" => $item]);
            }
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    public
    function postListe(RequestInterface $request, ResponseInterface $response)
    {
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

    public
    function getItem(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        $this->render($response, 'pages/items.twig', ['current_page' => "item"]);
    }

    public
    function postItem(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        if ($get[0] == "item") {
            if (isset($get[1])) {
                $img = strip_tags($_POST['img']);
                if (!empty($_POST['img']) && !str_contains(strip_tags($_POST['img']), "assets/img/")) {
                    $headers = @get_headers(strip_tags($_POST['img']));
                    if ($headers == false) {
                        $img = "assets/img/" . strip_tags($_POST['img']);
                    }
                }
                $item = Item::where('id', $get[1])
                    ->update(['nom' => strip_tags($_POST['nom']),
                        'descr' => strip_tags($_POST['description']),
                        'url' => strip_tags($_POST['url']),
                        'img' => $img,
                        'tarif' => strip_tags($_POST['tarif'])]);
            }
        } else {
            if (isset($get[1])) {
                $liste = Liste::where('token', $get[1])->first();
                if (isset($liste)) {
                    $item = new Item();
                    $obj = json_decode($liste);
                    $item->liste_id = $obj->no;
                    $item->nom = strip_tags($_POST['nom']);
                    $item->descr = strip_tags($_POST['description']);
                    $item->url = strip_tags($_POST['url']);
                    $item->tarif = strip_tags($_POST['tarif']);
                    $img = strip_tags($_POST['img']);
                    if (!empty($_POST['img']) && !str_contains(strip_tags($_POST['img']), "assets/img/")) {
                        $headers = @get_headers(strip_tags($_POST['img']));
                        if ($headers == false) {
                            $img = "assets/img/" . strip_tags($_POST['img']);
                        }
                    }
                    $item->img = $img;
                    $item->save();
                }
            }
        }
        //$this->redirect($response, 'home');
        $this->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }

    public
    function deleteItem(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        $item = Item::where('id', $get[1]);

        if ($item->count() == 1) {
            $item = $item->first();
            $reservation = Reservation::where('id_item', $get[1]);
            if ($reservation->count() == 1) {
                $this->render($response, 'pages/reserved.twig', ["current_page" => "reserved"]);
            } else {
                $item->delete();
                $this->render($response, 'pages/home.twig', ["current_page" => "home"]);
            }
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }
}