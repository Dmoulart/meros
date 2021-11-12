<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Vehicle;
use DateInterval;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    use MerosRepositoryExtension;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }



    /**
     * @param Vehicle $vehicle
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return bool
     */
    public function isAvailableDuringInterval(
        Vehicle $vehicle,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate): bool
    {
        /**
         * @var BookingRepository $bookingRepository
         */
        $bookingRepository = $this->getEntityManager()->getRepository(Booking::class);

        $bookings = $bookingRepository->getForVehicle($vehicle);
        $bookings = new ArrayCollection($bookings);

        return !$bookings->exists(function($i, $booking) use ($endDate, $startDate) {
            return $booking->getStartDate() >= $startDate && $booking->getEndDate() <= $endDate;
        });

    }
    // /**
    //  * @return Vehicle[] Returns an array of Vehicle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vehicle
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
