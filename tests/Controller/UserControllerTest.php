<?php

namespace App\Tests\Controller;

use App\DataFixtures\EntityGenerator;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Process;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerTest extends WebTestCase
{
    private EntityGenerator $entityGenerator;
    private EntityManagerInterface $em;
    private UserRepository $repository;

    // use ReloadDatabaseTrait;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->em->getRepository(User::class);

        $updateDb = new Process(['php bin/console doctrine:schema:update --force']);
        $updateDb->run();

        $populateDb = new Process(['php bin/console doctrine:fixtures:load --purge-with-truncate']);
        $populateDb->run();

    }

    /** @test */
    public function create(): void
    {
        self::ensureKernelShutdown();

        $user = [
            "names" => "Mabouno Wololo",
            "share" => 500,
            "roles" => ["ROLE_USER"],
            "email" => "Manono@test.com",
            "password" => "123456",
            "estimatedMileage" => 5000
        ];

        $client = static::createClient();

        $client->jsonRequest("POST","/users",($user));

        $this->assertResponseIsSuccessful();

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

        $user = [
            "names" => "Rollo Wololo",
            "share" => 500,
            "roles" => ["ROLE_USER"],
            "email" => "MaonoArg@test.com",
            "password" => "12345", //Test password can't be less than 6 chars
            "estimatedMileage" => 5000
        ];

        $client->jsonRequest("POST","/users",($user));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }

    /** @test */
    public function findAll(): void
    {
        self::ensureKernelShutdown();

        $users = $this->repository->findAll();

        $client = static::createClient();

        $client->request('GET','/users');

        $fetchedUsers = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($users, $fetchedUsers);

        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function findOne(): void
    {
        self::ensureKernelShutdown();

        $users = $this->repository->findAll();

        $firstUser = $users[0];

        $client = static::createClient();

        $client->request('GET','/users/'.$firstUser->getId());

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstUser = (object) $response[0];

        $this->assertEquals($firstUser->getEmail(), $fetchedFirstUser->email);
    }

    /** @test */
    public function failToFindOne(): void
    {
        self::ensureKernelShutdown();

        $users = $this->repository->findAll();

        $firstUser = $users[count($users) - 1];

        $client = static::createClient();

        $client->request('GET','/users/'.$firstUser->getId() + 1);

        $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    }
}
