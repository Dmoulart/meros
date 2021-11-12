<?php

namespace App\Test;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class MerosCrudTestCase extends WebTestCase
{
    protected static ServiceEntityRepositoryInterface $repository;
    protected static EntityManagerInterface $em;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
    }

    protected static function resetDatabase(string $class): void
    {
        self::bootKernel();

        self::$em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        self::$repository = self::$em->getRepository($class);

        $entities = self::$repository->findAll();

        foreach ($entities as $entity) {
            self::$em->remove($entity);
        }

        self::$em->flush();

        (new AppFixtures)->load(self::$em);
    }


}