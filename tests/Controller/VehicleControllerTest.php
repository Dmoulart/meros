<?php

namespace App\Tests\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Test\MerosCrudTestCase;

class VehicleControllerTest extends MerosCrudTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetDatabase(Booking::class);
        self::resetDatabase(Vehicle::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        self::resetDatabase(Booking::class);
        self::resetDatabase(Vehicle::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass(): void {
        self::resetDatabase(Booking::class);
        self::resetDatabase(Vehicle::class);
    }

    /** @test */
    public function canCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = [
            "name" => "WOLOLO",
            "model" => "159",
            "color" => "5437c9",
            "mileage" => 47010,
            "seats" => 2,
            "fuelType" => "diesel",
            "city" => "Torres",
            "street" =>  "rue Inès Lamy",
            "streetNumber"=> "1",
            "brand"=> "Alfa Romeo"
        ];

        $client->jsonRequest("POST","/vehicles",($vehicle));

        $response = json_decode($client->getResponse()->getContent(),true);

        $createdVehicle = $response["vehicle"];

        $this->assertEquals($vehicle['name'], $createdVehicle['name']);
    }

    /** @test */
    public function cannotCreateWithWrongMileageValue(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = [
            "model" => "159",
            "color" => "5437c9",
            "mileage" => -47010,
            "seats" => 2,
            "fuelType" => "diesel",
            "city" => "Torres",
            "street" =>  "rue Inès Lamy",
            "streetNumber"=> "1",
            "brand"=> "Alfa Romeo"
        ];

        $client->jsonRequest("POST","/vehicles",($vehicle));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }


    /** @test */
    public function canUpdate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = $this->getOneVehicle();

        $client->jsonRequest(
            "PUT",
            "/vehicles/".$vehicle->getId(),
            ['name' => "WOLOLO"]
        );

        $response = json_decode($client->getResponse()->getContent(),true);

        $updatedVehicle = $response["vehicle"];

        $this->assertEquals("WOLOLO", $updatedVehicle['name']);
    }

    /** @test */
    public function cannotUpdateWithWrongColorValue(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = $this->getOneVehicle();

        $client->jsonRequest(
            "PUT",
            "/vehicles/".$vehicle->getId(),
            ["color" => "12"]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    /** @test */
    public function canFindAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicles = self::$repository->findAll();

        $client->request('GET','/vehicles');

        $fetchedVehicles = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($vehicles, $fetchedVehicles);
    }

    /** @test */
    public function canFindOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle =  $this->getOneVehicle(2);

        $client->request('GET','/vehicles/'.$vehicle->getId());

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstVehicle = $response;

        $this->assertEquals($vehicle->getName(), $fetchedFirstVehicle['name']);
    }

    /** @test */
    public function cannotFindOneWithWrongIndex(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicles = self::$repository->findAll();

        $firstVehicle = $vehicles[count($vehicles) - 1];

        $client->request('GET','/vehicles/'.$firstVehicle->getId() + 1);

        $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    }
}
