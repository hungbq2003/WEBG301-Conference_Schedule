<?php

namespace App\Repository;

use App\Entity\Attendee;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attendee>
 */
class AttendeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendee::class);
    }

    public function findByConference(Conference $conference): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.conference = :conference')
            ->setParameter('conference', $conference)
            ->orderBy('a.lastName', 'ASC')
            ->addOrderBy('a.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByEmail(string $email): ?Attendee
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCheckedInByConference(Conference $conference): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.conference = :conference')
            ->andWhere('a.checkedIn = true')
            ->setParameter('conference', $conference)
            ->orderBy('a.checkedInAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByConference(Conference $conference): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.conference = :conference')
            ->setParameter('conference', $conference)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
