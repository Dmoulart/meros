<?php

namespace App\Tests\Controller;

use App\Entity\Booking;
use App\Entity\Expanse;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Test\MerosCrudTestCase;

class ExpanseControllerTest extends MerosCrudTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetDatabase(Booking::class);
        self::resetDatabase(Expanse::class);
        self::resetDatabase(Vehicle::class);
        self::$repository = self::$em->getRepository(Expanse::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        self::resetDatabase(Booking::class);
        self::resetDatabase(Expanse::class);
        self::resetDatabase(Vehicle::class);
        self::$repository = self::$em->getRepository(Expanse::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass(): void {
        self::resetDatabase(Booking::class);
        self::resetDatabase(Expanse::class);
        self::resetDatabase(Vehicle::class);
        self::$repository = self::$em->getRepository(Expanse::class);
    }

    /** @test */
    public function canCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanse = [
            "amount" => 209.1,
            "reason" => "réparation",
            "details" => "Plus de moteur.",
            "isSettled" => "false",
            "vehicle" => $this->getOneVehicle()->getId()
        ];

        $client->jsonRequest("POST","/expanses",($expanse));

        $response = json_decode($client->getResponse()->getContent(),true);

        $createdExpanse = $response["expanse"];

        $this->assertEquals($expanse['reason'], $createdExpanse['reason']);
    }

    /** @test */
    public function cannotCreateWithWrongAmountValue(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanse = [
            "amount" => -209.1,
            "reason" => "réparation",
            "details" => "Plus de moteur.",
            "isSettled" => false,
            "vehicle" => $this->getOneExpanse()->getId()
        ];

        $client->jsonRequest("POST","/expanses",($expanse));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }

    /** @test */
    public function cannotCreateWithNoVehicle(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanse = [
            "amount" => -209.1,
            "reason" => "réparation",
            "details" => "Plus de moteur.",
            "isSettled" => false,
        ];

        $client->jsonRequest("POST","/expanses",($expanse));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }


    /** @test */
    public function canUpdate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanse = $this->getOneExpanse();

        $client->jsonRequest(
            "PUT",
            "/expanses/".$expanse->getId(),
            ['reason' => "WOLOLO"]
        );

        $response = json_decode($client->getResponse()->getContent(),true);

        $updatedExpanse = $response["expanse"];

        $this->assertEquals("WOLOLO", $updatedExpanse['reason']);
    }

    /** @test */
    public function cannotUpdateBySettingNoVehicle(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanse = $this->getOneExpanse();

        $client->jsonRequest(
            "PUT",
            "/expanses/".$expanse->getId(),
            ["vehicle" => null]
        );

        $response = json_decode($client->getResponse()->getContent(),true);

        $updatedExpanse = $response["expanse"];

        $this->assertNotNull($updatedExpanse['vehicle']);
    }

    /** @test */
    public function canFindAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanses = self::$repository->findAll();

        $client->request('GET','/expanses');

        $fetchedExpanses = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($expanses, $fetchedExpanses);
    }

    /** @test */
    public function canFindOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $expanse =  $this->getOneExpanse(2);

        $client->request('GET','/expanses/'.$expanse->getId());

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstExpanse = $response;

        $this->assertEquals($expanse->getAmount(), $fetchedFirstExpanse['amount']);
    }
}
