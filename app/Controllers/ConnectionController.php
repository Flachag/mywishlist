<?php


namespace App\Controllers;


use App\Models\Utilisateur;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ConnectionController extends Controller
{
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
            if (Utilisateur::where('mail', '=', $mail)->exists()) throw new Exception("Cet email est déjà utilisée.");
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
            $response = $response->withRedirect($this->router->pathFor('loginPage'));
        }
        return $response->withRedirect($this->router->pathFor('loginPage'));
    }

    public function login(Request $request, Response $response, array $args): Response {
        try {
            if (isset($_SESSION['user'])) throw new BadMethodCallException("Vous êtes déjà connecté");
                $login = filter_var($request->getParsedBodyParam('login'), FILTER_SANITIZE_STRING);
                $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
                $user = User::where('login', '=', $login)->orWhere('mail', '=', $login)->firstOrFail();
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

    public function getInscription(Request $request, Response $response, $args){
        $this->view->render($response, 'pages/register.twig', [
            "current_page" => "register",
            "flash" => $this->flash->getMessages()
        ]);
    }
}