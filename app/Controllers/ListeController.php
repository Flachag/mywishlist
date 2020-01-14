<?php


namespace App\Controllers;


use App\models\Liste;
use App\Models\Message;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ListeController extends CookiesController
{
    /**
     * Méthode qui permet d'afficher une liste avec ses objets
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
     * Méthode qui permet d'ajouter un message publique à une liste
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

    /**
     * Méthode qui redirige vers la forme de création de liste
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function createForm(Request $request, Response $response, array $args){
        $this->view->render($response, 'pages/createListe.twig');
    }

    /**
     * Méthode permettant la création de liste
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
            if($request->getParsedBodyParam('public') != null){
                $public=true;
            }else {
                $public = false;
            }
            $this->loadCookiesFromRequest($request);

            if (new DateTime() > new DateTime($expiration)) throw new Exception("La date d'expiration ne peut être déjà passée.");

            $liste = new Liste();
            $liste->titre = $titre;
            $liste->description = $description;
            $liste->expiration = $expiration;
            $liste->token = bin2hex(random_bytes(10));
            $liste->token_edit = bin2hex(random_bytes(10));
            $liste->public = $public;
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

    /**
     * Méthode qui permet de modifier la liste
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function manageListe(Request $request, Response $response, array $args): Response{
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $expired = $liste->haveExpired();
            $this->loadCookiesFromRequest($request);

            if (!in_array($liste->token_edit, $this->getCreationTokens())) throw new Exception("Vous n'êtes pas le créateur de la liste.");
            if ($expired) throw new Exception("Cette liste est expirée.");

            $titre = filter_var($request->getParsedBodyParam('titre'), FILTER_SANITIZE_STRING);
            $descr = filter_var($request->getParsedBodyParam('description'), FILTER_SANITIZE_STRING);
            if($request->getParsedBodyParam('public') != null){
                $public=true;
            }else {
                $public = false;
            }
            Liste::where('token', '=', $args['token'])
                ->update(['titre' => $titre,
                    'description' => $descr,
                    'public' => $public]);

            $this->flash->addMessage('success', "La liste a été mise à jour!");
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }
        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * Méthode qui récupère les informations de la liste et des objets lors de l'edition
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function adminListe(Request $request, Response $response, array $args): Response {
        try {
            $liste = Liste::where('token', '=', $args['token'])->where('token_edit', '=', $args['token_edit'])->firstOrFail();
            $this->loadCookiesFromRequest($request);
            $this->view->render($response, 'pages/listeAdmin.twig', [
                "liste" => $liste,
                "items" => $liste->items()->get(),
                "creator" => in_array($liste->token_edit, $this->getCreationTokens()),
                "expiration" => $liste->haveExpired()
            ]);
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Cette liste n'existe pas...");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', "Une erreur est survenue, veuillez réessayer ultérieurement.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}