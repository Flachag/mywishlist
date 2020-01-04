<?php


namespace App\Controllers;


use App\models\Item;
use App\models\Liste;
use App\Models\Message;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ListeController extends CookiesController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getListe(Request $request, Response $response, array $args): Response {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $this->loadCookiesFromRequest($request);
            $this->view->render($response, 'pages/liste.twig', [
                "liste" => $liste,
                "items" => $liste->items()->get(),
                "creator" => in_array($liste->token_edit, $this->getCreationTokens()),
                "name" => $this->getName(),
                "expiration" => $liste->haveExpired(),
                "messages" => $liste->messages()->get()
            ]);
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Cette liste n'existe pas...");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }


    /**
     * Permet d'ajouter un message publique à une liste
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function addMessage(Request $request, Response $response, array $args): Response {
        try {
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING);
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);

            $liste = Liste::where('token', '=', $token)->firstOrFail();

            $msg = new Message();
            $msg->liste_id = $liste->no;
            $msg->message = $message;
            $msg->expediteur = $name;
            $msg->save();

            $this->flash->addMessage('success', "$name, Votre message a été envoyé");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
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

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function createForm(Request $request, Response $response, array $args){
        $this->view->render($response, 'pages/createListe.twig');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function createListe(Request $request, Response $response, array $args): Response {
        try {
            $titre = filter_var($request->getParsedBodyParam('titre'), FILTER_SANITIZE_STRING);
            $description = filter_var($request->getParsedBodyParam('description'), FILTER_SANITIZE_STRING);
            $expiration = $request->getParsedBodyParam('expiration');

            $this->loadCookiesFromRequest($request);

            if (new DateTime() > new DateTime($expiration)) throw new Exception("La date d'expiration ne peut être déjà passée.");

            $liste = new Liste();
            $liste->titre = $titre;
            $liste->description = $description;
            $liste->expiration = $expiration;
            $liste->token = bin2hex(random_bytes(10));
            $liste->token_edit = bin2hex(random_bytes(10));
            $liste->save();

            $this->addCreationToken($liste->token_edit);
            $response = $this->createResponseCookie($response);
            $link = $this->router->pathFor('liste', ['token' => $liste->token]);
            $this->flash->addMessage('success', "Votre liste a été créée! Cliquez <a href='$link'>ici</a> pour y accéder.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}