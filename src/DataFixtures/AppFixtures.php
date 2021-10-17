<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $generator = Factory::create('fr_FR');

        $admin = new User;
        $hashedPassword = $this->hasher->hashPassword($admin,'123456');
        $admin
            ->setPassword($hashedPassword)
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPseudo('Rick')
            ->setEmail('dorian.moulart@gmail.com')
            ->setNames('Dorian Moulart')
            ->setEstimatedMileage(2000)
            ->setCurrentMileage(450)
            ->setShare(500)
        ;

        $manager->persist($admin);

        for($i = 0;$i < 10;$i ++){
            $user = new User;
            $hashedPassword = $this->hasher->hashPassword($user,'123456');

            // A user can be two or more persons.
            $names = rand(0,1) > 0 ?
                $generator->firstName.' '.$generator->lastName
                :
                $generator->firstName.' '.$generator->lastName
                .' '.$generator->firstName.' '.$generator->lastName;

            $pseudo = rand(0,1) > 0 ? $generator->userName : null;
            $estimatedMileage = rand(500,2000);
            $currentMileage = rand(0,$estimatedMileage);
            $share = rand(1,10) * 100;

            $user
                ->setEmail($generator->email)
                ->setPassword($hashedPassword)
                ->setRoles(['ROLE_USER'])
                ->setNames($names)
                ->setPseudo($pseudo)
                ->setEstimatedMileage($estimatedMileage)
                ->setCurrentMileage($currentMileage)
                ->setShare($share)
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}
