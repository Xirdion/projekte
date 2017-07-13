<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Programmer;
use Doctrine\ORM\EntityRepository;

class BattleRepository extends EntityRepository
{
    /**
     * @param Programmer $programmer
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilderForProgrammer(Programmer $programmer) {
        return $this->createQueryBuilder('battle')
            ->andWhere('battle.programmer = :programmer')
            ->setParameter('programmer', $programmer);
    }
}
