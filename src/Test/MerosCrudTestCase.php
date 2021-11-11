<?php

namespace App\Test;

use App\DataFixtures\AppFixtures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class MerosCrudTestCase extends WebTestCase
{
    protected static ServiceEntityRepositoryInterface $repository;

    protected static function resetDatabase(string $class): void
    {
        self::bootKernel();

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        self::$repository = $em->getRepository($class);

        $entities = self::$repository->findAll();

        foreach ($entities as $entity) {
            $em->remove($entity);
        }

        $em->flush();

        (new AppFixtures)->load($em);
    }
}