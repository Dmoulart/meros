<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private static UserRepository $repository;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        self::bootKernel();

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        self::$repository = $em->getRepository(User::class);

        $users = self::$repository->findAll();

        foreach($users as $user){
            $em->remove($user);
        }

        $em->flush();

        (new AppFixtures)->load($em);
    }


    /** @test */
    public function create(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = [
            "names" => "Mabouno Wololo",
            "share" => 500,
            "roles" => ["ROLE_USER"],
            "email" => "Manono@test.com",
            "password" => "123456",
            "estimatedMileage" => 5000
        ];

        $client->jsonRequest("POST","/users",($user));

        $response = json_decode($client->getResponse()->getContent(),true);

        $createdUser = $response["user"];

        $this->assertEquals($user['email'], $createdUser['email']);
    }

    /** @test */
    public function failToCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = [
            "names" => "Rollo Wololo",
            "email" => "Froidure@test.com",
            "password" => "123456",
        ];

        $client->jsonRequest("POST","/users",($user));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }


    /** @test */
    public function update(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = self::$repository->findAll()[0];

        $client->jsonRequest(
            "PUT",
            "/users/".$user->getId(),
            ['email' => "wololo@test.com"]
        );

        $response = json_decode($client->getResponse()->getContent(),true);

        $updatedUser = $response["user"];

        $this->assertEquals("wololo@test.com", $updatedUser['email']);
    }

    /** @test */
    public function failToUpdate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = self::$repository->findAll()[0];

        $client->jsonRequest(
            "PUT",
            "/users/".$user->getId(),
            ["password" => "12"]
        );

        $this->assertResponseStatusCodeSame(411);
    }

    /** @test */
    public function findAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $users = self::$repository->findAll();

        $client->request('GET','/users');

        $fetchedUsers = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($users, $fetchedUsers);
    }

    /** @test */
    public function findOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $users = self::$repository->findAll();

        $user = $users[2];

        $client->request('GET','/users/'.$user->getId());

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstUser = $response;

        $this->assertEquals($user->getEmail(), $fetchedFirstUser['email']);
    }

    /** @test */
    public function failToFindOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $users = self::$repository->findAll();

        $firstUser = $users[count($users) - 1];

        $client->request('GET','/users/'.$firstUser->getId() + 1);

        $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    }
}
