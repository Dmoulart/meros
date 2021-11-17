<?php
namespace App\Controller;

use App\Auth\TokenDecoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends MerosController
{
    private TokenDecoder $tokenDecoder;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, TokenDecoder $tokenDecoder)
    {
        parent::__construct($em, $validator);
        $this->tokenDecoder = $tokenDecoder;
    }

    /**
     * @Route("/me", name="app_me", methods={"GET"})
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->tokenDecoder->getUserFromHeader($request);
        return $this->json($user);
    }
}