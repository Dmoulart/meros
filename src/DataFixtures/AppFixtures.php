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
    private EntityGenerator $entityGenerator;

    public function __construct(UserPasswordHasherInterface $hasher, EntityGenerator $entityGenerator)
    {
        $this->hasher = $hasher;
        $this->entityGenerator = $entityGenerator;
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
            $user = $this->entityGenerator->generate(User::class);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
