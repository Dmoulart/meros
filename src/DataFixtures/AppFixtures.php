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
            // ->setAlias('alpha')
            ->setEmail('dorian.moulart@gmail.com')
        ;

        $manager->persist($admin);

        for($i = 0;$i < 10;$i ++){
            $user = new User;
            $hashedPassword = $this->hasher->hashPassword($user,'123456');
            $user
                ->setEmail($generator->email)
                // ->setAlias($generator->userName)
                ->setPassword($hashedPassword)
                ->setRoles(['ROLE_USER'])
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}
