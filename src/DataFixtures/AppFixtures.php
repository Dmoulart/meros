<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $this->createUsers($manager);
        $this->createVehicles($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function createUsers(ObjectManager $manager): void
    {
        $admin = new User;

        $admin
            ->setPassword('123456')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPseudo('Rick')
            ->setEmail('dorian.moulart@gmail.com')
            ->setNames('Dorian Moulart')
            ->setEstimatedMileage(2000)
            ->setCurrentMileage(450)
            ->setShare(500);

        $manager->persist($admin);

        $entityGenerator = new EntityGenerator;

        $users = $entityGenerator->generate(User::class);

        foreach($users as $user){
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function createVehicles(ObjectManager $manager)
    {
        $entityGenerator = new EntityGenerator;

        $vehicles = $entityGenerator->generate(Vehicle::class);

        foreach($vehicles as $vehicle){
            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
