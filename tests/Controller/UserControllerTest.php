<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Test\MerosCrudTestCase;

class UserControllerTest extends MerosCrudTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetDatabase(User::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        self::resetDatabase(User::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass(): void {
        self::resetDatabase(User::class);
    }

    /** @test */
    public function canCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = [
            "names" => "Mabouno Wololo",
            "share" => 500,
            "roles" => ["ROLE_USER"],
            "email" => "Manono@test.com",
            "password" => "123456",
            "estimatedMileage" => 5000,
            "bookings" => []
        ];

        $client->jsonRequest("POST","/users",($user));

        $response = json_decode($client->getResponse()->getContent(),true);

        $createdUser = $response["user"];

        $this->assertEquals($user['email'], $createdUser['email']);
    }

    /** @test */
    public function cannotCreateWithIncompleteInformations(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = [
            "names" => "Rollo Wololo",
            "email" => "Froidure@test.com",
            "password" => "123456",
            "bookings" => []
        ];

        $client->jsonRequest("POST","/users",($user));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }


    /** @test */
    public function canUpdate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = $this->getOneUser();

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
    public function cannotUpdateWithWrongPassword(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = $this->getOneUser();

        $client->jsonRequest(
            "PUT",
            "/users/".$user->getId(),
            ["password" => "12"]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    /** @test */
    public function canFindAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $users = self::$repository->findAll();

        $client->request('GET','/users');

        $fetchedUsers = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($users, $fetchedUsers);
    }

    /** @test */
    public function canFindOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = $this->getOneUser(2);

        $client->request('GET','/users/'.$user->getId());

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstUser = $response;

        $this->assertEquals($user->getEmail(), $fetchedFirstUser['email']);
    }

    /** @test */
    public function cannotFindOneWithWrongIndex(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $users = self::$repository->findAll();

        $firstUser = $users[count($users) - 1];

        $client->request('GET','/users/'.$firstUser->getId() + 1);

        $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    }
}
