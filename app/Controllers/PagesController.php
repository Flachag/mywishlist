<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PagesController extends Controller {

    public function home(RequestInterface $request, ResponseInterface $response){
        $this->render($response,'pages/home.twig');
    }
}