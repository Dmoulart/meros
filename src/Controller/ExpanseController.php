<?php
namespace App\Controller;

use App\Entity\expanse;
use App\Repository\ExpanseRepository;
use App\Repository\VehicleRepository;
use App\Utils\Req;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExpanseController extends MerosController
{

    private VehicleRepository $vehicleRepository;

    private ExpanseRepository $repository;

    function __construct(VehicleRepository $vehicleRepository,
                         ExpanseRepository $repository,
                         EntityManagerInterface $em,
                         ValidatorInterface $validator){
        parent::__construct($em, $validator);
        $this->vehicleRepository = $vehicleRepository;
        $this->repository = $repository;
    }

    /**
     * @Route("/expanses/{id}", name="app_expanses_get", methods={"GET"})
     */
    function find(int|string|null $id = null): Response
    {
        $expanses = $this->repository->findOneOrAll($id);

        if(!$expanses) return $this->json(
             $id ? 'Cannot find expanse with this id' : 'Cannot find expanses'
            ,404
        );
        return $this->json($expanses);
    }

    /**
     * @Route("/expanses/{id}", name="app_expanses_delete", methods={"DELETE"})
     */
    function remove(int|string|null $id = null): Response
    {
        $expanses = $this->repository->findOneOrAll($id);

        if(!$expanses) return $this->json(
            $id ? 'Cannot find expanse with this id' : 'Cannot find expanses'
            ,404
        );

        $deletedExpanses = $this->repository->removeOneOrAll($expanses);

        $this->em->flush();

        return $this->json([
            $id ? 'expanses successfully deleted' : 'expanse successfully deleted',
            "expanse" => $deletedExpanses
        ]);
    }

    /**
     * @Route("/expanses", name="app_expanses_create", methods={"POST"})
     */
    function create(Request $request): Response
    {
            /** @var Expanse $expanse */
            $expanse = Req::toEntity(
                $request,
                Expanse::class,
                [
                    "ignore" => ["vehicle"]
                ]
            );

            $vehicleId = $request->get('vehicle');

            if(!$vehicleId) return $this->json(
                'There is no vehicle associated with this expanse'
            , 422);

            $vehicle = $this->vehicleRepository->find($vehicleId);

            if(!$vehicle)
            {
               return $this->json("Cannot find the vehicle associated with this expanse.", 404);
            }

            $expanse->setVehicle($vehicle);

            $errors = $this->validator->validate($expanse);

            if (count($errors)) return $this->json($errors, 422);

            $this->em->persist($expanse);

            $this->em->flush();

            return $this->json([
                'Expanse successfully created',
                'expanse' =>  $expanse,
            ]);
    }

    /**
     * @Route("/expanses/{id}", name="app_expanses_update", methods={"PUT"})
     */
    function update(Request $request, int $id): Response
    {
            $expanse = $this->repository->find($id);

            if(!$expanse) return $this->json(
                'Cannot find this expanse'
                ,404
            );

            /** @var expanse $expanse */
            $expanse = Req::toEntity($request,
                expanse::class,
                [
                    "ignore" => ["vehicle"]
                ],
                $expanse
            );

            if(($vehicleId = $request->get('vehicle'))){
                $vehicle = $this->vehicleRepository->find($vehicleId);

                if(!$vehicle)
                {
                    return $this->json("Cannot find the vehicle associated with this expanse.", 404);
                }

                $expanse->setVehicle($vehicle);
            }

            $errors = $this->validator->validate($expanse);

            if (count($errors)) return $this->json($errors, 422);

            $this->em->persist($expanse);

            $this->em->flush();

            return $this->json([
                'expanse successfully updated',
                'expanse' =>  $expanse,
            ]);
    }
}