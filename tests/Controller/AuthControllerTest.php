<?php

namespace App\Tests\Controller;

use App\Auth\TokenDecoder;
use App\Entity\User;
use App\Test\MerosCrudTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\LcobucciJWSProvider;

class AuthControllerTest extends MerosCrudTestCase
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
    public function canLoginUser(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = $this->getOneUser();

        $authData = [
            "username" => $user->getEmail(),
            "password" => $user->getPassword(),
        ];

        $client->jsonRequest("GET","api/login_check",($authData));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 200);
    }

    /** @test */
    public function canCreateLogAndExtractUserFromToken(): void
    {
        // Todo: Split this test methods
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

        // Create a user
        $client->jsonRequest("POST","/users",($user));

        $authData = [
            "username" => $user['email'],
            "password" => $user['password']
        ];

        // Login new user
        $client->jsonRequest("GET","api/login_check",($authData));

        $response = json_decode($client->getResponse()->getContent(),true);

        $token = $response['token'];
        //Todo: When we'll implement the authorizations on the different controllers we'll need
        // to refactor this and use this to authorize the client before passing every test
        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf("Bearer %s", $token));

        $client->jsonRequest("GET","/me");

        $response = json_decode($client->getResponse()->getContent(),true);

        $username = $response['username'];

        $this->assertEquals($username, $authData['username']);
    }

}
