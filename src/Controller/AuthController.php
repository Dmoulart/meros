<?php
namespace App\Controller;

use App\Auth\TokenDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends MerosController
{

    /**
     * @Route("/me", name="app_me", methods={"GET"})
     */
    public function me(Request $request, TokenDecoder $decoder){
        $token = $request->headers->get('Authorization');
        $token = $request->get
        return $this->json($decoder->decode($token));
    }
}