<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function statistics (EventRepository $eventRepository)
    {
        $allEvents = $eventRepository->countAllEvents();

        $noVerified = $eventRepository->countAllEventsVerified(0);
        $blocked = $eventRepository->countAllEventsVerified(1);
        $active = $eventRepository->countAllEventsVerified(2);
            
        $statusActive = $eventRepository->countAllEventsStatus(0);
        $statusCanceled = $eventRepository->countAllEventsStatus(1);
        $statusFinished = $eventRepository->countAllEventsStatus(2);

        $stats = [
            'allEvents' => $allEvents,
            'blocked' => $blocked,
            'active' => $active,
            'noVerified' => $noVerified,
            'statusActive' => $statusActive,
            'statusCanceled' => $statusCanceled,
            'statusFinished' => $statusFinished,
        ];

        return $stats;
    }

    public function countAllEvents(): int
    {
        return $this->createQueryBuilder('e')
            ->select('count(e)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAllEventsValid(): int
    {

        return $this->createQueryBuilder('e')
            ->select('count(e)')
            ->where('e.is_verified = 2')
            ->andWhere('e.status = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAllEventsVerified(?int $is_verified = null): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.is_verified)')
            ->where('v.is_verified = :is_verified')
            ->setParameter('is_verified', $is_verified)
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }

    public function countAllEventsStatus(?int $status = null): int
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.status)')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
        ;
    }


    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
