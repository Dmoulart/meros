<?php

namespace App\Tests\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Test\MerosCrudTestCase;

class BookingControllerTest extends MerosCrudTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetDatabase(Vehicle::class);
        self::resetDatabase(Booking::class);

    }

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        self::resetDatabase(Booking::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass(): void {
        self::resetDatabase(Booking::class);
    }

    /** @test */
    public function create(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $vehicleRepo = parent::$em->getRepository(Vehicle::class);
        $userRepo = parent::$em->getRepository(User::class);
        $vehicle = $vehicleRepo->findAll()[0];
        $user = $userRepo->findAll()[0];
        $booking = [
            "informations" => "WOLOLO",
            "isOpen" => "false",
            "startDate" => "2021-11-12 01:04:06",
            "endDate" => "2021-11-12 03:04:06",
            "startMileage" => 1232,
            "endMileage" => 1432,
            "isCompleted" => true,
            "vehicle" =>  $vehicle->getId(),
            "users"=> [$user->getId()],
        ];

        $client->jsonRequest("POST","/bookings",($booking));

        $response = json_decode($client->getResponse()->getContent(),true);

        $createdBooking = $response["booking"];

        $this->assertEquals($booking['informations'], $createdBooking['informations']);
    }

    /** @test */
    public function failToCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $vehicleRepo = parent::$em->getRepository(Vehicle::class);
        $userRepo = parent::$em->getRepository(User::class);
        $vehicle = $vehicleRepo->findAll()[0];
        $user = $userRepo->findAll()[0];
        $booking = [
            "informations" => "WOLOLO",
            "isOpen" => "false",
            "startDate" => "2021-11-12 01:04:06",
            "endDate" => "2021-11-12 03:04:06",
            "startMileage" => -1232,
            "endMileage" => -1432,
            "isCompleted" => true,
            "vehicle" =>  $vehicle->getId(),
            "users"=> [$user->getId()],
        ];

        $client->jsonRequest("POST","/vehicles",($booking));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }


    /** @test */
    public function update(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $booking = self::$repository->findAll()[0];

        $client->jsonRequest(
            "PUT",
            "/bookings/".$booking->getId(),
            ['informations' => "WOUAW"]
        );

        $response = json_decode($client->getResponse()->getContent(),true);

        $updatedBooking = $response["booking"];

        $this->assertEquals("WOUAW", $updatedBooking['informations']);
    }

    /** @test */
    public function failToUpdate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $booking = self::$repository->findAll()[0];

        $client->jsonRequest(
            "PUT",
            "/bookings/".$booking->getId(),
            ["startMileage" => -12]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    /** @test */
    public function findAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $bookings = self::$repository->findAll();

        $client->request('GET','/bookings');

        $fetchedBookings = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($bookings, $fetchedBookings);
    }

    /** @test */
    public function findOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $bookings = self::$repository->findAll();

        /**
         * @var Booking $booking
         */
        $booking = $bookings[2];

        $client->request('GET','/bookings/'.$booking->getId());

        $response = json_decode($client->getResponse()->getContent(),true);

        $fetchedFirstBooking = $response;

        $this->assertEquals($booking->getId(), $fetchedFirstBooking['id']);
    }

    /** @test */
    public function failToFindOne(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $bookings = self::$repository->findAll();

        $firstBooking = $bookings[count($bookings) - 1];

        $client->request('GET','/bookings/'.$firstBooking->getId() + 1);

        $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    }
}
