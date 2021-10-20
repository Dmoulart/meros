<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EntityGenerator
{
    function generate(string $class): ?User
    {
        return match ($class) {
            User::class => $this->generateUser(),
            default => null,
        };
    }

    private function generateUser(): User
    {
        $generator = Factory::create('fr_FR');

        $user = new User;

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

        return $user
            ->setEmail($generator->email)
            ->setPassword('123456')
            ->setRoles(['ROLE_USER'])
            ->setNames($names)
            ->setPseudo($pseudo)
            ->setEstimatedMileage($estimatedMileage)
            ->setCurrentMileage($currentMileage)
            ->setShare($share)
        ;
    }
}