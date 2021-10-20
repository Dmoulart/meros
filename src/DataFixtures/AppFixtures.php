<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $admin = new User;

        $admin
            ->setPassword('123456')
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPseudo('Rick')
            ->setEmail('dorian.moulart@gmail.com')
            ->setNames('Dorian Moulart')
            ->setEstimatedMileage(2000)
            ->setCurrentMileage(450)
            ->setShare(500)
        ;

        $manager->persist($admin);

        $entityGenerator = new EntityGenerator;

        for($i = 0;$i < 10;$i ++){
            $user = $entityGenerator->generate(User::class);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
