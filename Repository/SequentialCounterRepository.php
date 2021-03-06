<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Repository;

use Doctrine\ORM\EntityRepository;
use HoPeter1018\SequentialCounterFormatBundle\Entity\SequentialCounter;

class SequentialCounterRepository extends EntityRepository
{
    public function getNext($entityClass, $start, $batch)
    {
        $entity = $this->findOneBy([
          'batch' => $batch,
          'entityFqcn' => $entityClass,
        ]);
        if (null === $entity) {
            $entity = new SequentialCounter();
            $entity->setBatch($batch)->setCounter($start)->setEntityFqcn($entityClass);
        } else {
            $entity->setCounter($entity->getCounter() + 1);
        }
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity->getCounter();
    }
}
