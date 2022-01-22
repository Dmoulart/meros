<?php
namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use App\Utils\Req;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

class BookingController extends MerosController
{

    private BookingRepository $repository;
    private VehicleRepository $vehicleRepository;
    private UserRepository $userRepository;
    private PaginatorInterface $paginator;

    function __construct(BookingRepository $repository,
                        VehicleRepository $vehicleRepository,
                        UserRepository $userRepository,
                        EntityManagerInterface $em,
                        ValidatorInterface $validator,
                        PaginatorInterface $paginator){
        parent::__construct($em, $validator);
        $this->repository = $repository;
        $this->vehicleRepository = $vehicleRepository;
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/bookings", name="app_bookings_get", methods={"GET"})
     */
    function findSome(Request $request): Response
    {
        $bookings = $this->repository->findAll();

        $page = $request->get('page') ?? 1;

        if(!$bookings) return $this->json('Cannot find bookings', 404);

        $response = $this->paginator->paginate(
            $bookings, 
            $page, 
            10
        );

        if(!count($response)) return $this->json('Cannot find bookings', 404);

        return $this->json($response);
    }

    /**
     * @Route("/bookings/{id}", name="app_bookings_getOne", methods={"GET"}, requirements={"id"="\d+"})
     */
    function findOne(int $id): Response
    {
        $bookings = $this->repository->find($id);

        if(!$bookings) return $this->json('Cannot find booking with this id',404);

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

            try
            {
                $booking = $this->addVehicle($vehicleId, $booking);
            }
            catch (Exception $e)
            {
                return $this->json($e->getMessage(),$e->getCode());
            }

            $usersId = $request->get('users');

            if(!$usersId || !count($usersId)) return $this->json(
                'There is no users associated with this booking.'
                ,422
            );

            try
            {
                $booking = $this->addUsers($usersId,$booking);
            }
            catch(Exception $e)
            {
                return $this->json(
                    $e->getMessage()
                    ,422
                );
            }

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
                ["ignore" => ["vehicle"]],
                $booking
            );

            //todo : factorize this (same in create)
            if(($vehicleId = $request->get('vehicle')))
            {
                try
                {
                    $booking = $this->addVehicle($vehicleId, $booking);
                }
                catch (Exception $e)
                {
                    return $this->json($e->getMessage(),$e->getCode());
                }
            }

            if(($usersId = $request->get('users')) && count($usersId))
            {
                try
                {
                    $booking = $this->addUsers($usersId,$booking);
                }
                catch(Exception $e)
                {
                    return $this->json(
                        $e->getMessage()
                        ,422
                    );
                }
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

    /**
     * @throws Exception
     */
    private function addUsers(array $usersIds, Booking $booking): Booking
    {
        $clonedBooking = clone $booking;

        foreach ($usersIds as $userId) {
            $user = $this->userRepository->find($userId);

            if (!$user) throw new Exception('Cannot find user associated with this booking.');

            if ($this->userRepository->hasAlreadyBookedDuringInterval(
                $user, $clonedBooking->getStartDate(),
                $clonedBooking->getEndDate())
            ) {
                throw new Exception("User $user has already booked another car during this interval.");
            }

            $clonedBooking->addUser($user);
        }
        return $clonedBooking;
    }

    /**
     * @throws Exception
     */
    private function addVehicle(int $vehicleId, Booking $booking): Booking
    {
        $clonedBooking = clone $booking;

        $vehicle = $this->vehicleRepository->find($vehicleId);

        if(!$vehicle)
        {
            throw new Exception("Cannot find the vehicle associated with this booking.", 404);
        }
        if(!$this->vehicleRepository->isAvailableDuringInterval(
            $vehicle,
            $clonedBooking->getStartDate(),
            $clonedBooking->getEndDate())
        )
        {
            throw new Exception("The vehicle $vehicle is not available during this time interval.", 422);
        }

        $clonedBooking->setVehicle($vehicle);

        return $clonedBooking;
    }
}