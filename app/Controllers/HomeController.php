<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends MainController
{

    public function home(RequestInterface $request, ResponseInterface $response)
    {
        $this->view->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }
}