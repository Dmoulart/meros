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

class UserController extends AbstractController
{

    private UserRepository $repository;

    private EntityManagerInterface $em;

    function __construct(UserRepository $repository, EntityManagerInterface $em ){
        $this->repository = $repository;
        $this->em = $em;
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

        return Res::json($users);
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
    function create(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            /** @var User $user */
            $user = Req::toEntity($request, User::class, [
                'required' => [
                    'estimatedMileage',
                    'email',
                    'password',
                    'names',
                    'share'
                ]
            ]);

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

        } catch (EntityConverterException $e) {
            return $e->getResponse();
        }
    }

    /**
     * @Route("/users/{id}", name="app_users_update", methods={"PUT"})
     */
    function update(Request $request, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {

            $user = $this->repository->find($id);

            if(!$user) return Res::json(
                'Cannot find user with this id'
                ,404
            );

            /** @var User $user */
            $user = Req::toEntity($request, User::class, [
                'ignore' => ['password']
            ], $user);

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

        } catch (EntityConverterException $e) {
            return $e->getResponse();
        }
    }
}