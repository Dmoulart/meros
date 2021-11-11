<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Req;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends MerosController
{
    private UserRepository $repository;

    function __construct(UserRepository $repository,
                         EntityManagerInterface $em,
                         ValidatorInterface $validator){
       parent::__construct($em, $validator);
       $this->repository = $repository;
    }

    /**
     * @Route("/users/{id}", name="app_users_get", methods={"GET"})
     */
    function find(int|string|null $id = null): Response
    {
        $users = $this->repository->findOneOrAll($id);

        if(!$users) return $this->json(
             $id ? 'Cannot find user with this id' : 'Cannot find users'
            ,404
        );

        return $this->json($users);
    }

    /**
     * @Route("/users/{id}", name="app_users_delete", methods={"DELETE"})
     */
    function remove(int|string|null $id = null): Response
    {
        $users = $this->repository->findOneOrAll($id);

        if(!$users) return $this->json(
            $id ? 'Cannot find this user' : 'Cannot find any user'
            ,404
        );

        $deletedUsers = $this->repository->removeOneOrAll($users);

        $this->em->flush();

        return $this->json([
            $id ? 'User successfully deleted' : 'Users successfully deleted',
            "users" => $deletedUsers]
        );
    }

    /**
     * @Route("/users", name="app_users_create", methods={"POST"})
     */
    function create(Request $request,
                    UserPasswordHasherInterface $passwordHasher): Response
    {
            /** @var User $user */
            $user = Req::toEntity($request, User::class);

            $errors = $this->validator->validate($user);

            if (count($errors)) return $this->json($errors, 411);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            $this->em->persist($user);

            $this->em->flush();

            return $this->json([
                'User successfully created',
                'user' =>  $user,
            ]);
    }

    /**
     * @Route("/users/{id}", name="app_users_update", methods={"PUT"})
     */
    function update(Request $request, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
            $user = $this->repository->find($id);

            if(!$user) return $this->json(
                'Cannot find user with this id'
                ,404
            );

            /** @var User $user */
            $user = Req::toEntity($request, User::class, [
                'ignore' => ['password']
            ], $user);

            $errors = $this->validator->validate($user);

            if (count($errors)) return $this->json($errors, 411);

            if(($password = $request->get('password'))){
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);
            }

            $this->em->persist($user);

            $this->em->flush();

            return $this->json([
                'User successfully updated',
                'user' =>  $user,
            ]);

    }
}