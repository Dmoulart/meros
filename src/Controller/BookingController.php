<?php
namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\BookingRepository;
use App\Repository\VehicleRepository;
use App\Utils\Req;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookingController extends MerosController
{

    private BookingRepository $repository;
    private VehicleRepository $vehicleRepository;

    function __construct(BookingRepository $repository,
                         VehicleRepository $vehicleRepository,
                         EntityManagerInterface $em,
                         ValidatorInterface $validator){
        parent::__construct($em, $validator);
        $this->repository = $repository;
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * @Route("/bookings/{id}", name="app_bookings_get", methods={"GET"})
     */
    function find(int|string|null $id = null): Response
    {
        $bookings = $this->repository->findOneOrAll($id);

        if(!$bookings) return $this->json(
             $id ? 'Cannot find booking with this id' : 'Cannot find bookings'
            ,404
        );

        return $this->json($bookings);
    }

    /**
     * @Route("/bookings/{id}", name="app_bookings_delete", methods={"DELETE"})
     */
    function remove(int|string|null $id = null): Response
    {
        $bookings = $this->repository->findOneOrAll($id);

        if(!$bookings) return $this->json(
            $id ? 'Cannot find booking with this id' : 'Cannot find bookings'
            ,404
        );

        $deletedVehicles = $this->repository->removeOneOrAll($bookings);

        $this->em->flush();

        return $this->json([
            $id ? 'Booking successfully deleted' : 'Bookings successfully deleted',
            "bookings" => $deletedVehicles
        ]);
    }

    /**
     * @Route("/bookings", name="app_bookings_create", methods={"POST"})
     */
    function create(Request $request): Response
    {
            /** @var Booking $booking */
            $booking = Req::toEntity($request, Booking::class);

            $errors = $this->validator->validate($booking);

            if (count($errors)) return $this->json($errors, 422);

            $vehicleId = $request->get('vehicle');

            if(!$vehicleId) return $this->json(
                 'There is no vehicle associated with this booking.'
                ,422
            );

            $vehicle = $this->vehicleRepository->find($vehicleId);

            if(!$vehicle) return $this->json(
                "Cannot find the vehicle associated with this booking."
                ,404
            );

            if(!$this->vehicleRepository->isAvailableDuringInterval(
                $vehicle,
                $booking->getStartDate(),
                $booking->getEndDate())
            ) return $this->json(
                "The vehicle $vehicle is not available during this time interval."
                ,422
            );

            $booking->setVehicle($vehicle);

            $this->em->persist($booking);

            $this->em->flush();

            return $this->json([
                'Booking successfully created',
                'booking' =>  $booking,
            ]);
    }

    /**
     * @Route("/bookings/{id}", name="app_bookings_update", methods={"PUT"})
     */
    function update(Request $request, int $id): Response
    {
            $booking = $this->repository->find($id);

            if(!$booking) return $this->json(
                'Cannot find this booking'
                ,404
            );

            /** @var Booking $booking */
            $booking = Req::toEntity($request,
                Booking::class,
                null,
                $booking
            );

            //todo : factorize this (same in create)
            if(($vehicleId = $request->get('vehicle'))){
                $vehicle = $this->vehicleRepository->find($vehicleId);

                if(!$vehicle) return $this->json(
                    "Cannot find the vehicle associated with this booking."
                    ,404
                );

                if(!$this->vehicleRepository->isAvailableDuringInterval(
                    $vehicle,
                    $booking->getStartDate(),
                    $booking->getEndDate())
                ) return $this->json(
                    "The vehicle $vehicle is not available during this time interval."
                    ,422
                );

                $booking->setVehicle($vehicle);
            }

            $errors = $this->validator->validate($booking);

            if (count($errors)) return $this->json($errors, 422);

            $this->em->persist($booking);

            $this->em->flush();

            return $this->json([
                'Booking successfully updated',
                'booking' =>  $booking,
            ]);
    }
}