<?php
namespace App\Controller;

use App\Entity\User;
use App\Exceptions\EntityConverterException;
use App\Repository\UserRepository;
use App\Utils\Req;
use App\Utils\Res;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    private UserRepository $repository;

    private EntityManagerInterface $em;

    private ValidatorInterface $validator;

    function __construct(UserRepository $repository, EntityManagerInterface $em, ValidatorInterface $validator){
        $this->repository = $repository;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @Route("/users/{id}", name="app_users_get", methods={"GET"})
     */
    function find(int|string|null $id = null): Response
    {
        $users = $this->repository->findOneOrAll($id);

        if(!$users) return Res::json(
             $id ? 'Cannot find user with this id' : 'Cannot find users'
            ,404
        );
        return $this->json($users);
       // return Res::json($users);
    }

    /**
     * @Route("/users/{id}", name="app_users_delete", methods={"DELETE"})
     */
    function remove(int|string|null $id = null): Response
    {
        $users = $this->repository->findOneOrAll($id);

        if(!$users) return Res::json(
            $id ? 'Cannot find user with this id' : 'Cannot find users'
            ,404
        );

        $deletedUsers = clone $users;

        $this->em->remove($users);
        $this->em->flush();

        return Res::json([
            $id ? 'User successfully deleted' : 'Users successfully deleted',
            "users" => $deletedUsers
        ]);
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

            if (count($errors)) return Res::json($errors, 411);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            $this->em->persist($user);

            $this->em->flush();

            return Res::json([
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

            if(!$user) return Res::json(
                'Cannot find user with this id'
                ,404
            );

            /** @var User $user */
            $user = Req::toEntity($request, User::class, [
                'ignore' => ['password']
            ], $user);

            $errors = $this->validator->validate($user);

            if (count($errors)) return Res::json($errors, 411);

            if(($password = $request->get('password'))){
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);
            }

            $this->em->persist($user);

            $this->em->flush();

            return Res::json([
                'User successfully updated',
                'user' =>  $user,
            ]);

    }
}