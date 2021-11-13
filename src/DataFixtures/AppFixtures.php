<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Expanse;
use App\Entity\User;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    static EntityGenerator $entityGenerator;

    public function load(ObjectManager $manager)
    {
        static::$entityGenerator = new EntityGenerator($manager);

        $this->createUsers($manager);
        $this->createVehicles($manager);
        $this->createBookings($manager);
        $this->createExpanses($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function createUsers(ObjectManager $manager): void
    {
        $admin = new User;

 /*       $admin
            ->setPassword('123456')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPseudo('Rick')
            ->setEmail('dorian.moulart@gmail.com')
            ->setNames('Dorian Moulart')
            ->setEstimatedMileage(2000)
            ->setCurrentMileage(450)
            ->setShare(500);

        $manager->persist($admin);*/


        $users = static::$entityGenerator->generate(User::class);

        foreach($users as $user){
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function createVehicles(ObjectManager $manager)
    {
        $vehicles = static::$entityGenerator->generate(Vehicle::class);

        foreach($vehicles as $vehicle){
            $manager->persist($vehicle);
        }

        $manager->flush();
    }

    private function createBookings(ObjectManager $manager)
    {
        $bookings = static::$entityGenerator->generate(Booking::class);

        foreach($bookings as $booking){
            $manager->persist($booking);
        }

        $manager->flush();
    }

    private function createExpanses(ObjectManager $manager)
    {
        $expanses = static::$entityGenerator->generate(Expanse::class);

        foreach($expanses as $expanse){
            $manager->persist($expanse);
        }

        $manager->flush();
    }
}
