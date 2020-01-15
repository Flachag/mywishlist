<?php


namespace App\Controllers;


use App\Models\Item;
use App\Models\Liste;
use App\Models\Utilisateur;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ConnectionController
 * @package App\Controllers
 */
class ConnectionController extends CookiesController
{

    /**
     * Méthode qui permet d'inscire un utilisateur
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function inscription(Request $request, Response $response, $args): Response{
        try{

            $nom = filter_var($request->getParsedBodyParam('nom'), FILTER_SANITIZE_STRING);
            $prenom = filter_var($request->getParsedBodyParam('prenom'), FILTER_SANITIZE_STRING);
            $mail = filter_var($request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
            $pseudo = filter_var($request->getParsedBodyParam('pseudo'), FILTER_SANITIZE_STRING);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
            $passwordconfirm = filter_var($request->getParsedBodyParam('passwordconfirm'), FILTER_SANITIZE_STRING);

            if (mb_strlen($pseudo, 'utf8') < 2 || mb_strlen($pseudo, 'utf8') > 35) throw new Exception("Votre pseudo doit contenir entre 2 et 35 caractères.");
            if (Utilisateur::where('login', '=', $pseudo)->exists()) throw new Exception("Ce pseudo est déjà pris.");
            if (Utilisateur::where('mail', '=', $mail)->exists()) throw new Exception("Cette email est déjà utilisée.");
            if ($password != $passwordconfirm) throw new Exception("La confirmation du mot de passe n'est pas bonne");
            if (mb_strlen($password, 'utf8') < 8) throw new Exception("Votre mot de passe doit contenir au moins 8 caractères");

            $user = new Utilisateur();
            $user->nom = $nom;
            $user->prenom = $prenom;
            $user->mail = $mail;
            $user->login = $pseudo;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->save();
            $this->flash->addMessage('success', "Le compte a bien été créé !");
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            return $response->withRedirect($this->router->pathFor('inscriptionPage'));
        }
        return $response->withRedirect($this->router->pathFor('loginPage'));
    }

    /**
     * Méthode qui permet de connecter un utilisateur
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function login(Request $request, Response $response, array $args): Response {
        try {
            if (isset($_SESSION['user'])) throw new BadMethodCallException("Vous êtes déjà connecté");

            $login = filter_var($request->getParsedBodyParam('username'), FILTER_SANITIZE_STRING);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
            $user = Utilisateur::where('login', '=', $login)->orWhere('mail', '=', $login)->firstOrFail();

            if(!password_verify($password, $user->password)) throw new Exception('Login ou Mot de Passe incorrect');
            $_SESSION['user'] = $user;
            $response = $response->withRedirect($this->router->pathFor('accountPage'));
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', 'Aucun compte associé à cet identifiant n\'a été trouvé.');
            $response = $response->withRedirect($this->router->pathFor('loginPage'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('loginPage'));
        }
        return $response;
    }

    /**
     * Méthode qui redirige vers la page login
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getLogin(Request $request, Response $response, array $args) {
        $this->loadCookiesFromRequest($request);
        $this->view->render($response, 'pages/login.twig', [
            "current_page" => "login",
            "flash" => $this->flash->getMessages(),
        ]);
    }

    /**
     * Méthode qui redirige vers la page d'inscription
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function getInscription(Request $request, Response $response, $args){
        $this->view->render($response, 'pages/register.twig', [
            "current_page" => "register",
            "flash" => $this->flash->getMessages()
        ]);
    }

    /**
     * Méthode qui permet de déconnecter un utilisateur
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function logout(Request $request, Response $response, array $args): Response {
        unset($_SESSION['user']);
        $this->flash->addMessage('success', 'Vous vous êtes deconnecté');
        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
     * Méthode qui récupère les informations du compte
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function getAccount(Request $request, Response $response, $args){
        $this->view->render($response, 'pages/account.twig', [
            "current_page" => "account",
            "flash" => $this->flash->getMessages()
        ]);
    }

    public function manageAccount(Request $request, Response $response, $args){
        if (!isset($_SESSION['user'])) throw new BadMethodCallException("Vous n'êtes pas connecté.");
        $user = $_SESSION['user'];
        if($user->id != $args['id']) throw new Exception("Vous ne possédez pas le bon compte.");
        if ($_POST['action'] == 'delete') {
            try {
                $util = Utilisateur::where('id', '=', $args['id'])->firstOrFail();
                $listes = $util->listes()->get();
                if(isset($listes)){
                    foreach ($listes as $liste){
                        $items = $liste->items()->get();
                        foreach ($items as $item){
                            $item->delete();
                        }
                        $liste->delete();
                    }
                }
                $this->logout($request, $response, $args);
                $util->delete();
                $this->flash->addMessage('success', "Le compte a été supprimée");
                $response = $response->withRedirect($this->router->pathFor('home'));
            } catch (Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
                $response = $response->withRedirect($this->router->pathFor('home'));
            }
        }else if($_POST['action'] == 'edit'){

        }else{
            $this->flash->addMessage('error', "Une erreur est survenue, veuillez réessayer ultérieurement.");
        }
        return $response->withRedirect($this->router->pathFor('home'));
    }
}