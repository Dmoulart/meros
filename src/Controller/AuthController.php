<?php
namespace App\Controller;

use App\Auth\TokenDecoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\UserRepository;

class AuthController extends MerosController
{
    private TokenDecoder $tokenDecoder;
    private UserRepository $repository;

    public function __construct(EntityManagerInterface $em, 
                                UserRepository $repository,
                                ValidatorInterface $validator, 
                                TokenDecoder $tokenDecoder)
    {
        parent::__construct($em, $validator);
        $this->tokenDecoder = $tokenDecoder;
        $this->repository = $repository;
    }
    
    /**
     * @Route("/me", name="app_me", methods={"GET"})
     */
    public function me(Request $request): JsonResponse
    {
        try
        {
            $data = $this->tokenDecoder->getUserDataFromHeader($request);
            
            if(!$data || count($data) === 0)
                return $this->json("Cannot identify user", 401);
            
            $user = $this->repository->findByEmail($data['username']);
            
            $authData = [
                "user" => $user,
                "token" => [
                    "exp" => $data["exp"],
                    "iat" => $data["iat"]
                ]
            ];
        }
        catch(\Exception $e){
            return $this->json(["error" => $e->getMessage()], 401);
        }
        return $this->json($authData, 200, [], ['groups' => ['user_read']]);
    }
}