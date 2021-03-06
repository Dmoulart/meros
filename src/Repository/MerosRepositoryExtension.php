<?php

namespace App\Repository;

use Doctrine\ORM\ORMException;

trait MerosRepositoryExtension
{

    /**
     * @param Object|array $entities
     * @return array|Object|null
     * @throws ORMException
     */
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

    public function findOneOrAll(?int $id): array|Object|null
    {
        return $id ? $this->find($id)
            :
            $this->findAll();
    }

    public function findRange(array $range): array|Object|null
    {
        $items = $this->findAll();
        return array_slice($items, $range[0], $range[1]);
    }

}