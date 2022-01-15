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
    public function canCreate(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $user = $this->getOneUser();
        $vehicle = $this->getOneVehicle();
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
        // dump($response);
        $createdBooking = $response["booking"];

        $this->assertEquals($booking['informations'], $createdBooking['informations']);
    }

    /** @test */
    public function canRegisterUser(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $user = $this->getOneUser();
        $vehicle = $this->getOneVehicle();
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

        $this->assertEquals($user->getEmail(), $createdBooking['users'][0]['email']);
    }

    /** @test */
    public function canRegisterVehicle(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $user = $this->getOneUser();
        $vehicle = $this->getOneVehicle();
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

        $this->assertEquals($vehicle->getModel(), $createdBooking['vehicle']['model']);
    }

    /** @test */
    public function cannotBookUnavailableVehicle(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $user = $this->getOneUser();
        $vehicle = $this->getOneVehicle();
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

        $booking2 = [
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

        $client->jsonRequest("POST","/bookings",($booking2));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }

    /** @test */
    public function cannotCreateTwoDuringSameIntervalForSameUser(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();
        $user = $this->getOneUser();
        $vehicle = $this->getOneVehicle();
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
        $vehicle2 = $this->getOneVehicle(1);

        $bookingWithSameUserAtSameDate = [
            "informations" => "WOLOLO",
            "isOpen" => "false",
            "startDate" => "2021-11-12 01:04:06",
            "endDate" => "2021-11-12 03:04:06",
            "startMileage" => 1232,
            "endMileage" => 1432,
            "isCompleted" => true,
            "vehicle" =>  $vehicle2->getId(),
            "users"=> [$user->getId()],
        ];

        $client->jsonRequest("POST","/bookings",($bookingWithSameUserAtSameDate));

        $response = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }

    /** @test */
    public function cannotCreateWithWrongMileageValues(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $user = $this->getOneUser();
        $vehicle = $this->getOneVehicle();
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
    public function cannotCreateWithNoUser(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $vehicle = $this->getOneVehicle();

        $booking = [
            "informations" => "WOLOLO",
            "isOpen" => "false",
            "startDate" => "2021-11-12 01:04:06",
            "endDate" => "2021-11-12 03:04:06",
            "startMileage" => 1232,
            "endMileage" => 1432,
            "isCompleted" => true,
            "vehicle" =>  $vehicle->getId(),
            "users"=> [],
        ];

        $client->jsonRequest("POST","/vehicles",($booking));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }

    /** @test */
    public function cannotCreateWithNoVehicle(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $user = $this->getOneUser();

        $booking = [
            "informations" => "WOLOLO",
            "isOpen" => "false",
            "startDate" => "2021-11-12 01:04:06",
            "endDate" => "2021-11-12 03:04:06",
            "startMileage" => 1232,
            "endMileage" => 1432,
            "isCompleted" => true,
            "users"=> [$user->getId()],
        ];
        $client->jsonRequest("POST","/vehicles",($booking));

        $this->assertResponseStatusCodeSame($client->getResponse()->getStatusCode(), 422);
    }

    /** @test */
    public function canUpdateVehicle(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $booking = self::$repository->findAll()[0];

        $vehicle = $this->getOneVehicle();

        $client->jsonRequest(
            "PUT",
            "/bookings/".$booking->getId(),
            ['vehicle' => $vehicle->getId()]
        );

        $response = json_decode($client->getResponse()->getContent(),true);

        $updatedBooking = $response["booking"];

        $this->assertEquals($vehicle->getId(), $updatedBooking['vehicle']['id']);
    }


    /** @test */
    public function canUpdate(): void
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
    public function cannotUpdateWithWrongMileageValues(): void
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
    public function canFindAll(): void
    {
        self::ensureKernelShutdown();

        $client = static::createClient();

        $bookings = self::$repository->findAll();

        $client->request('GET','/bookings');

        $fetchedBookings = json_decode($client->getResponse()->getContent(),true);

        $this->assertSameSize($bookings, $fetchedBookings);
    }

    /** @test */
    public function canFindOne(): void
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
    // public function cannotFindOneWithWrongIndex(): void
    // {
    //     self::ensureKernelShutdown();

    //     $client = static::createClient();

    //     $bookings = self::$repository->findAll();

    //     $firstBooking = $bookings[count($bookings) - 1];

    //     $client->request('GET','/bookings/'.$firstBooking->getId() + 1);

    //     $this->assertResponseStatusCodeSame(404, $client->getResponse()->getStatusCode());
    // }


}
