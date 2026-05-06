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

    /**
     * Search conferences whose name or location matches the query.
     * Returns all conferences ordered by start date when the query is empty.
     */
    public function search(?string $query): array
    {
        $qb = $this->createQueryBuilder('c')->orderBy('c.startDate', 'ASC');

        $query = trim((string) $query);
        if ($query !== '') {
            $qb->andWhere('LOWER(c.name) LIKE :q OR LOWER(c.location) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($query) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
