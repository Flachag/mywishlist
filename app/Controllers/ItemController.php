<?php

namespace App\Controllers;

use App\Models\Reservation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Item;
use App\Models\Liste;
use Slim\Http\Request;
use Slim\Http\Response;

/**
* Class ItemController
 * @package App\Controllers
*/
class ItemController extends CookiesController {

    /**
     * Methode qui permet d'afficher un item
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getItem(Request $request, Response $response, array $args): Response {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $item = Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->firstOrFail();
            $reserver = $item->book && !$liste->haveExpired() && !in_array($liste->token_edit, $this->getCreationTokens());
            $this->loadCookiesFromRequest($request);
            $message = "";
            if ($item->book){
                $message = $item->reservation()->get()->first()->message;
            }
            $this->view->render($response, 'pages/item.twig', [
                "liste" => $liste,
                "item" => $item,
                "reserver" => $reserver,
                "name" => $this->getName(),
                "expiration" => $liste->haveExpired(),
                "message" => $message,
                "creator" => in_array($liste->token_edit, $this->getCreationTokens()),
            ]);
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Cet objet n'existe pas...");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', "Une erreur est survenue, veuillez réessayer ultérieurement.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Methode qui permet de reserver un item
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function bookItem(Request $request, Response $response, array $args): Response {
        try {
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);

            $liste = Liste::where('token', '=', $token)->firstOrFail();
            $this->loadCookiesFromRequest($request);

            if(in_array($liste->token_edit, $this->getCreationTokens())) throw new Exception("Vous ne pouvez pas réserver un objet de votre prore liste.");

            if ($liste->haveExpired()) throw new Exception("Cette liste a déjà expiré, il n'est plus possible de réserver des objets.");
            if (Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé.");

            $r = new Reservation();
            $r->item_id = $item_id;
            $r->message = $message;
            $r->nom = $name;
            $r->save();

            $this->changeName($name);
            $response = $this->createResponseCookie($response);

            $this->flash->addMessage('success', "$name, votre réservation a été enregistrée !");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', 'Nous n\'avons pas pu trouver cet objet.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
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
                        $dataImg = getimagesize($_POST['img']);
                        var_dump($dataImg);
                        if (!isset($dataImg)) {
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

    public function deleteItem(RequestInterface $request, ResponseInterface $response, $args)
    {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $item = Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->firstOrFail();
            $reserver = $item->book && !$liste->haveExpired() && !in_array($liste->token_edit, $this->getCreationTokens());
            $this->loadCookiesFromRequest($request);

            if (!in_array($liste->token_edit, $this->getCreationTokens())) throw new Exception("Vous n'êtes pas le créateur de la liste.");
            if ($reserver) throw new Exception("Cet objet est réservé.");

            $item->delete();

            $this->flash->addMessage('success', "L'objet a été supprimée!");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}