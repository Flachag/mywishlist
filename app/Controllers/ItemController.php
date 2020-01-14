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
class ItemController extends CookiesController
{

    /**
     * Methode qui permet d'afficher un item
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getItem(Request $request, Response $response, array $args): Response
    {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $item = Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->firstOrFail();
            $reserver = $item->book && !$liste->haveExpired() && !in_array($liste->token_edit, $this->getCreationTokens());
            $this->loadCookiesFromRequest($request);
            $message = "";
            if ($item->book) {
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
    public function bookItem(Request $request, Response $response, array $args): Response
    {
        try {
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);

            $liste = Liste::where('token', '=', $token)->firstOrFail();
            $this->loadCookiesFromRequest($request);

            if (in_array($liste->token_edit, $this->getCreationTokens())) throw new Exception("Vous ne pouvez pas réserver un objet de votre prore liste.");

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

    private function isUrlImage($str) : bool {
        $flag = true;
        if(isset($str) && !empty($str)) {
            $array = getimagesize($str);
            if (!isset($array) || empty($array)) {
                $flag = false;
            }
        }
        return $flag;
    }

    public function manageItem(Request $request, Response $response, $args): Response
    {
        if ($_POST['action'] == 'delete') {
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
        } elseif ($_POST['action'] == 'edit') {
            try {
                $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
                $item = Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->firstOrFail();
                $reserver = $item->book && !$liste->haveExpired() && !in_array($liste->token_edit, $this->getCreationTokens());
                $this->loadCookiesFromRequest($request);

                if (!in_array($liste->token_edit, $this->getCreationTokens())) throw new Exception("Vous n'êtes pas le créateur de la liste.");
                if ($reserver) throw new Exception("Cet objet est réservé.");

                $nom = filter_var($request->getParsedBodyParam('nom'), FILTER_SANITIZE_STRING);
                $descr = filter_var($request->getParsedBodyParam('descr'), FILTER_SANITIZE_STRING);
                $url = filter_var($request->getParsedBodyParam('url'), FILTER_SANITIZE_URL);
                $tarif = filter_var($request->getParsedBodyParam('tarif'), FILTER_VALIDATE_FLOAT);

                $img = filter_var($request->getParsedBodyParam('img'), FILTER_SANITIZE_STRING);
                if($this->isUrlImage($img)){
                    $image = filter_var($request->getParsedBodyParam('img'), FILTER_SANITIZE_URL);
                }else{
                    $image = "/mywishlist/public/img/" . $img;
                }

                $item = Item::where('id', $args['id'])
                    ->update(['nom' => $nom,
                        'descr' => $descr,
                        'url' => $url,
                        'img' => $image,
                        'tarif' => $tarif]);

                $this->flash->addMessage('success', "L'objet a été mis à jour!");
                $response = $response->withRedirect($this->router->pathFor('home'));
            } catch (Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
                $response = $response->withRedirect($this->router->pathFor('home'));
            }
        } else {
            $this->flash->addMessage('error', "Une erreur est survenue, veuillez réessayer ultérieurement.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    public function ajoutItem(Request $request, Response $response, $args): Response
    {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $expired = $liste->haveExpired();
            $this->loadCookiesFromRequest($request);

            if (!in_array($liste->token_edit, $this->getCreationTokens())) throw new Exception("Vous n'êtes pas le créateur de la liste.");
            if ($expired) throw new Exception("Cette liste est expirée.");

            $nom = filter_var($request->getParsedBodyParam('nom'), FILTER_SANITIZE_STRING);
            $descr = filter_var($request->getParsedBodyParam('descr'), FILTER_SANITIZE_STRING);
            $url = filter_var($request->getParsedBodyParam('url'), FILTER_SANITIZE_URL);
            $tarif = filter_var($request->getParsedBodyParam('tarif'), FILTER_VALIDATE_FLOAT);

            $img = filter_var($request->getParsedBodyParam('img'), FILTER_SANITIZE_STRING);
            //verifier si img est une url pour hotlinking
            if($this->isUrlImage($img)){
                $image = filter_var($request->getParsedBodyParam('img'), FILTER_SANITIZE_URL);
            }else{
                $image = "/mywishlist/public/img/" . $img;
            }

            $item = new Item();
            $item->liste_id = $liste->no;
            $item->nom = $nom;
            $item->descr = $descr;
            $item->url = $url;
            $item->img = $image;
            $item->tarif = $tarif;
            $item->save();

            $this->flash->addMessage('success', "L'objet a été mis à jour!");
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }
        return $response->withRedirect($this->router->pathFor('home'));
    }
}
