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
        $this->render($response, 'pages/listes.twig', ["current_page" => "voir_listes", "listes" => Liste::all()]);
        $items = Item::all();
        foreach ($items as $item) {
            if (!empty($item->img) && !str_contains($item->img, "/mywishlist/public/assets/img/")) {
                $headers = get_headers($item->img);
                if (!$headers || strpos($headers[0], '404')) {
                    $img = "/mywishlist/public/assets/img/" . $item->img;
                    $item->img = $img;
                    $item->save();
                }
            }
        }
    }

    public function getListeManage(RequestInterface $request, ResponseInterface $response, $args)
    {
        $liste = null;
        if (array_key_exists('token', $args)) {
            $liste = Liste::where('token', $args['token']);
            if ($liste->count() == 1) {
                $liste = $liste->first();
                $this->render($response, 'pages/listeManage.twig', ["current_page" => "listeManage", "liste" => $liste]);
            } else {
                $this->render($response, 'pages/404.twig', ["current_page" => "404"]);
            }
        } else {
            $this->render($response, 'pages/listeManage.twig', ["current_page" => "listeManage", "liste" => $liste]);
        }
    }

    public function postListe(RequestInterface $request, ResponseInterface $response, $args)
    {
        if (array_key_exists('token', $args)) {
            $liste = Liste::where('token', $args['token'])
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
}