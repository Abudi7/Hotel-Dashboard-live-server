<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }


 /**
 * Find available rooms by date range.
 *
 * @param \DateTimeInterface $startDate
 * @param \DateTimeInterface $endDate
 * @return mixed
 */
public function findAvailableRoomsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
{
    $qb = $this->createQueryBuilder('b')
        ->select('r')
        ->from('App\Entity\Rooms', 'r')
        ->leftJoin('b.Rooms', 'room') // Changed alias from 'r' to 'room'
        ->where('b.startdate NOT BETWEEN :start AND :end')
        ->andWhere('b.enddate NOT BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate);

    return $qb->getQuery()->getResult();
}

/**
 * Find and delete bookings within a date range.
 *
 * @param \DateTimeInterface $startDate
 * @param \DateTimeInterface $endDate
 * @return void
 */
public function deleteBookingsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
{
    $bookings = $this->createQueryBuilder('b')
        ->select('b')
        ->where('b.startdate BETWEEN :start AND :end')
        ->orWhere('b.enddate BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->getQuery()
        ->getResult();

    foreach ($bookings as $booking) {
        $this->getEntityManager()->remove($booking);
    }
    $this->getEntityManager()->flush();
}


     /**
     * Find the latest booking.
     *
     * @return Booking|null The latest booking entity or null if no booking is found.
     */
    public function findLatestBooking(): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

     // Custom method to fetch bookings with associated room and user data
     public function findBookingsWithRoomAndUser(): array
     {
         return $this->createQueryBuilder('b')
             ->leftJoin('b.Rooms', 'r')
             ->addSelect('r')
             ->getQuery()
             ->getResult();
     }

    //    /**
    //     * @return Booking[] Returns an array of Booking objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
