<?php

namespace App\Controller;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MerosController extends AbstractController
{
    protected EntityManagerInterface $em;
    protected ValidatorInterface $validator;

    function __construct(EntityManagerInterface $em, ValidatorInterface $validator){
        $this->em = $em;
        $this->validator = $validator;
    }
}