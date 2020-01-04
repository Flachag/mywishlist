<?php


namespace App\Controllers;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ReservationController extends Controller
{
    public function getReservation(RequestInterface $request, ResponseInterface $response){
        $this->view->render($response, 'pages/reservation.twig', ["current_page" => "reservation"]);
    }
}