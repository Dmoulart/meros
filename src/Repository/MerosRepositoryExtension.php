<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

trait MerosRepositoryExtension
{
    public function removeOneOrAll(Object | array $entities): array|Object|null
    {
        if(is_array($entities)) {
            $deletedEntities =  $entities;
            $deletedEntities = (object) $deletedEntities;
            foreach($entities as $entity){
                $this->_em->remove($entity);
            }
        }
        else{
            $deletedEntities = clone $entities;
            $this->_em->remove($entities);
        }

        return $deletedEntities;
    }
}