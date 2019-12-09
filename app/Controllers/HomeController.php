<?php

namespace App\Controllers;

use App\models\Item;
use App\models\Liste;
use App\Models\Reservation;
use http\Env\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends MainController
{

    public function home(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/home.twig', ["current_page" => "home"]);
    }
}