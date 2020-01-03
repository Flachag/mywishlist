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
                $this->view->render($response, 'pages/listeManage.twig', ["current_page" => "listeManage", "liste" => $liste]);
            } else {
                $this->view->render($response, 'pages/404.twig', ["current_page" => "404"]);
            }
        } else {
            $this->view->render($response, 'pages/listeManage.twig', ["current_page" => "listeManage", "liste" => $liste]);
        }
    }


    // je comprends pas pourquoi y en a 2 pour créer ici
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