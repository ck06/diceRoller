<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DialogueStep;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class DialogueStepRepository extends EntityRepository
{
    /**
     * @throws NonUniqueResultException
     */
    public function getRoot(?string $tree = null): DialogueStep
    {
        $qb = $this->createQueryBuilder('ds');

        if ($tree !== null) {
            $qb->andWhere('ds.tree = :tree')->setParameter('tree', $tree);
        }

        return $qb
            ->andWhere('ds.previous is null')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
