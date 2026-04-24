<?php

namespace App\Repository;

use App\Entity\Speaker;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Speaker>
 */
class SpeakerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Speaker::class);
    }

    public function findByConference(Conference $conference): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.conference = :conference')
            ->setParameter('conference', $conference)
            ->orderBy('s.lastName', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByEmail(string $email): ?Speaker
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
