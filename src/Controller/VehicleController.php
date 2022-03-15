<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use App\Utils\Req;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

class VehicleController extends MerosController
{

    private VehicleRepository $repository;
    private PaginatorInterface $paginator;

    function __construct(VehicleRepository $repository,
                         EntityManagerInterface $em,
                         ValidatorInterface $validator,
                         PaginatorInterface $paginator){
        parent::__construct($em, $validator);
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/vehicles", name="app_vehicles_get", methods={"GET"})
     */
    function findSome(Request $request): Response
    {
        $vehicles = $this->repository->findAll();

        $page = $request->get('page') ?? 1;

        if(!$vehicles) return $this->json('Cannot find bookings', 404);

        $response = $this->paginator->paginate(
            $vehicles, 
            $page, 
            5
        );

        if(!count($response)) return $this->json('Cannot find vecicles', 404);

        return $this->json($response, 200, [],['groups' => ['booking_read']]);
    }

    /**
     * @Route("/bookings/{id}", name="app_bookings_getOne", methods={"GET"}, requirements={"id"="\d+"})
     */
    function findOne(int $id): Response
    {
        $vehicle = $this->repository->find($id);

        if(!$vehicle) return $this->json('Cannot find booking with this id',404);

        return $this->json($vehicle, 200, []);
    }

    /**
     * @Route("/vehicles/{id}", name="app_vehicles_delete", methods={"DELETE"})
     */
    function remove(int|string|null $id = null): Response
    {
        $vehicles = $this->repository->findOneOrAll($id);

        if(!$vehicles) return $this->json(
            $id ? 'Cannot find vehicle with this id' : 'Cannot find vehicles'
            ,404
        );

        $deletedVehicles = $this->repository->removeOneOrAll($vehicles);

        $this->em->flush();

        return $this->json([
            $id ? 'Vehicles successfully deleted' : 'Vehicle successfully deleted',
            "vehicles" => $deletedVehicles
        ]);
    }

    /**
     * @Route("/vehicles", name="app_vehicles_create", methods={"POST"})
     */
    function create(Request $request): Response
    {
            /** @var Vehicle $vehicle */
            $vehicle = Req::toEntity($request, Vehicle::class);

            $errors = $this->validator->validate($vehicle);

            if (count($errors)) return $this->json($errors, 422);

            $this->em->persist($vehicle);

            $this->em->flush();

            return $this->json([
                'Vehicle successfully created',
                'vehicle' =>  $vehicle,
            ]);
    }

    /**
     * @Route("/vehicles/{id}", name="app_vehicles_update", methods={"PUT"})
     */
    function update(Request $request, int $id): Response
    {
            $vehicle = $this->repository->find($id);

            if(!$vehicle) return $this->json(
                'Cannot find this vehicle'
                ,404
            );

            /** @var Vehicle $vehicle */
            $vehicle = Req::toEntity($request,
                Vehicle::class,
                null,
                $vehicle
            );

            $errors = $this->validator->validate($vehicle);

            if (count($errors)) return $this->json($errors, 422);

            $this->em->persist($vehicle);

            $this->em->flush();

            return $this->json([
                'Vehicle successfully updated',
                'vehicle' =>  $vehicle,
            ]);
    }
}