<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findByConference(Conference $conference): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.conference = :conference')
            ->setParameter('conference', $conference)
            ->orderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByTrack(Conference $conference, string $track): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.conference = :conference')
            ->andWhere('s.track = :track')
            ->setParameter('conference', $conference)
            ->setParameter('track', $track)
            ->orderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingSessionsByConference(Conference $conference): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.conference = :conference')
            ->andWhere('s.startTime >= :now')
            ->setParameter('conference', $conference)
            ->setParameter('now', new \DateTime())
            ->orderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
