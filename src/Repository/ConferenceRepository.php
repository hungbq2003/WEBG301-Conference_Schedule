<?php

namespace App\Repository;

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conference>
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    public function findUpcomingConferences(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate >= :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('c.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastConferences(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.endDate < :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('c.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
