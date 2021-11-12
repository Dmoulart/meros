<?php

namespace App\Tests\Controller;

use App\Entity\Vehicle;
use App\Test\MerosCrudTestCase;

class VehicleControllerTest extends MerosCrudTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        self::resetDatabase(Vehicle::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        self::resetDatabase(Vehicle::class);
    }

    /** @test */
    public function create(): void
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
    public function failToCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = [
            "model" => "159",
            "color" => "5437c9",
            "mileage" => -47010, // We use a non conform value
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
    public function update(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = self::$repository->findAll()[0];

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
    public function failToUpdate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicle = self::$repository->findAll()[0];

        $client->jsonRequest(
            "PUT",
            "/vehicles/".$vehicle->getId(),
            ["color" => "12"]
        );

        $this->assertResponseStatusCodeSame(411);
    }

    /** @test */
    public function findAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicles = self::$repository->findAll();

        $client->request('GET','/vehicles');

        $fetchedVehicles = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($vehicles, $fetchedVehicles);
    }

    /** @test */
    public function findOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicles = self::$repository->findAll();

        /**
         * @var Vehicle $vehicle
         */
        $vehicle = $vehicles[2];

        $client->request('GET','/vehicles/'.$vehicle->getId());

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstVehicle = $response;

        $this->assertEquals($vehicle->getName(), $fetchedFirstVehicle['name']);
    }

    /** @test */
    public function failToFindOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicles = self::$repository->findAll();

        $firstVehicle = $vehicles[count($vehicles) - 1];

        $client->request('GET','/vehicles/'.$firstVehicle->getId() + 1);

        $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    }
}
