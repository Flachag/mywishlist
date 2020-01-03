<?php

namespace App\Controllers;

use App\models\Item;
use App\models\Liste;
use App\Models\Reservation;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ItemController extends MainController
{
    public function getItems(RequestInterface $request, ResponseInterface $response, $args)
    {

        $liste = Liste::where('no', $args['no']);
        $token = "";
        $listeTemp = null;
        if (array_key_exists('token', $args)) {
            $token = $args['token'];
            $listeTemp = Liste::where('token', $args['token'])->first();
        }
        if ($token != "" && $listeTemp != ($liste->first())) {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        } else {
            if (!array_key_exists('id', $args) && array_key_exists('no', $args) && sizeof($args) <= 2 && $liste->count() == 1) {
                $liste = $liste->first();
                $items = $liste->items;
                $this->render($response, 'pages/items.twig', ["current_page" => "voir_objets",
                    "items" => $items,
                    "liste" => $liste,
                    "token" => $token]);
            } elseif (array_key_exists('no', $args) && array_key_exists('id', $args) && sizeof($args) <= 3 && $liste->count() == 1) {
                $items = $liste->first()->items;
                foreach ($items as $item) {
                    if ($item->id == $args['id']) {
                        $find = true;
                        $this->render($response, 'pages/item.twig', ["current_page" => "item",
                            "item" => $item,
                            "liste" => $liste->first(),
                            "token" => $token]);
                    }
                }
                if (!isset($find)) {
                    $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
                }
            } else {
                $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
            }
        }
    }

    public function getItemManage(RequestInterface $request, ResponseInterface $response, $args)
    {
        if (array_key_exists('token', $args)) {
            $item = null;
            if (array_key_exists('id', $args)) {
                $item = Item::where('id', $args['id']);
                if ($item->count() == 1) {
                    $item = $item->first();
                    $reservation = Reservation::where('id_item', $args['id']);
                    if ($reservation->count() == 1) {
                        $this->render($response, 'pages/reserved.twig', ["current_page" => "reserved"]);
                    } else {
                        $this->render($response, 'pages/itemManage.twig', ["current_page" => "itemManage", "item" => $item]);
                    }
                } else {
                    $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
                }
            } else {
                $this->render($response, 'pages/itemManage.twig', ["current_page" => "itemManage", "item" => $item]);
            }
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }

    public function postItem(RequestInterface $request, ResponseInterface $response, $args)
    {
        if (array_key_exists('token', $args)) {
            $liste = Liste::where('token', $args['token']);
            if ($liste->count() == 1) {
                $liste = $liste->first();
                $item = null;
                if (array_key_exists('id', $args)) {
                    $img = strip_tags($_POST['img']);
                    if (!empty($_POST['img']) && !str_contains(strip_tags($_POST['img']), "/mywishlist/public/assets/img/")) {
                        $headers = get_headers($_POST['img']);
                        if (!$headers || strpos($headers[0], '404')) {
                            $img = "/mywishlist/public/assets/img/" . strip_tags($_POST['img']);
                        }
                    }
                    $item = Item::where('id', $args['id'])
                        ->update(['nom' => strip_tags($_POST['nom']),
                            'descr' => strip_tags($_POST['description']),
                            'url' => strip_tags($_POST['url']),
                            'img' => $img,
                            'tarif' => strip_tags($_POST['tarif'])]);
                } else {
                    $item = new Item();
                    $obj = json_decode($liste);
                    $item->liste_id = $obj->no;
                    $item->nom = strip_tags($_POST['nom']);
                    $item->descr = strip_tags($_POST['description']);
                    $item->url = strip_tags($_POST['url']);
                    $item->tarif = strip_tags($_POST['tarif']);
                    $img = strip_tags($_POST['img']);
                    if (!empty($_POST['img']) && !str_contains(strip_tags($_POST['img']), "/mywishlist/public/assets/img/")) {
                        $headers = get_headers($_POST['img']);
                        if (!$headers || strpos($headers[0], '404')) {
                            $img = "/mywishlist/public/assets/img/" . strip_tags($_POST['img']);
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
    function deleteItem(RequestInterface $request, ResponseInterface $response, $args)
    {
        if (array_key_exists('id', $args)) {
            $item = Item::where('id', $args['id']);
            if ($item->count() == 1) {
                $item = $item->first();
                $reservation = Reservation::where('id_item', $args['id']);
                if ($reservation->count() == 1) {
                    $this->render($response, 'pages/reserved.twig', ["current_page" => "reserved"]);
                } else {
                    $item->delete();
                    $this->render($response, 'pages/home.twig', ["current_page" => "home"]);
                }
            } else {
                $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
            }
        } else {
            $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }
}