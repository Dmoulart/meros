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
        try
        {
            $data = $this->tokenDecoder->getUserDataFromHeader($request);
            
            if(!$data)
                return $this->json("Cannot identify user", 401);
            
            $authData = [
                "user" => [
                    "email" => $data["username"],
                    "roles" => $data["roles"]
                ],
                "token" => [
                    "exp" => $data["exp"],
                    "iat" => $data["iat"]
                ]
            ];
        }
        catch(\Exception $e){
            return $this->json(["error" => $e->getMessage()], 401);
        }
        return $this->json($authData);
    }
}