<?php

namespace App\Controllers;

use App\models\Item;
use App\models\Liste;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ItemController extends MainController
{
    public function getItemCreate(RequestInterface $request, ResponseInterface $response, $args)
    {
        try {
            $liste = Liste::where('token', $args['token'])->firstOrFail();
            $this->view->render($response, 'pages/itemCreate.twig', ["current_page" => "itemCreate", "liste" => $liste]);
        } catch (ModelNotFoundException $e){
            throw new NotFoundException($request, $response);
        }
    }

    public function getItem(RequestInterface $request, ResponseInterface $response, $args)
    {
        try {
            $liste = Liste::where('no', $args['no'])->firstOrFail();
            $token = "";
            if (array_key_exists('token', $args)) {
                $token = $args['token'];
            }
            if (!array_key_exists('id', $args) && array_key_exists('no', $args) && sizeof($args) <= 2) {
                $items = $liste->items;
                $this->view->render($response, 'pages/items.twig', ["current_page" => "voir_objets",
                    "items" => $items,
                    "liste" => $liste,
                    "token" => $token]);
            } elseif (array_key_exists('no', $args) && array_key_exists('id', $args) && sizeof($args) <= 3) {
                $items = $liste->items;
                foreach ($items as $item) {
                    if ($item->id == $args['id']) {
                        $this->view->render($response, 'pages/item.twig', ["current_page" => "item",
                            "item" => $item,
                            "liste" => $liste,
                            "token" => $token]);
                    }
                }
            }
        } catch (ModelNotFoundException $e){
            throw new NotFoundException($request, $response);
        }
    }

    public function getItemManage(RequestInterface $request, ResponseInterface $response, $args)
    {
        try {
            $item = Item::where('id', $args['id'])->firstOrFail();
            if ($item->count() == 1) {
                $reservation = Reservation::where('id_item', $args['id']);
                if ($reservation->count() == 1) {
                    $this->view->render($response, 'pages/reserved.twig', ["current_page" => "reserved"]);
                } else {
                    $this->view->render($response, 'pages/itemManage.twig', ["current_page" => "itemManage", "item" => $item]);
                }
            } else {
                $liste = Liste::where('token', $args['token'])->firstOrFail();
                $this->view->render($response, 'pages/itemManage.twig', ["current_page" => "itemManage", "item" => $item, "liste" => $liste]);
            }
        }catch (ModelNotFoundException $e){
            throw new NotFoundException($request, $response);
        }
    }

    public function postItem(RequestInterface $request, ResponseInterface $response)
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
                        $headers = @get_headers($_POST['img']);
                        echo var_dump($headers);
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
        $this->view->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }

    public function deleteItem(RequestInterface $request, ResponseInterface $response)
    {
        $get = explode("=", $request->getUri()->getQuery());
        $item = Item::where('id', $get[1]);
        if ($item->count() == 1) {
            $item = $item->first();
            $reservation = Reservation::where('id_item', $get[1]);
            if ($reservation->count() == 1) {
                $this->view->render($response, 'pages/reserved.twig', ["current_page" => "reserved"]);
            } else {
                $item->delete();
                $this->view->render($response, 'pages/home.twig', ["current_page" => "home"]);
            }
        } else {
            $this->view->render($response, 'pages/404.twig', ["current_page" => "404"]);
        }
    }
}