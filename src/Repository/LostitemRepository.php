<?php

namespace App\Repository;

use App\Entity\Lostitem;
use App\Entity\Rooms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
/**
 * @extends ServiceEntityRepository<Lostitem>
 */
class LostitemRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Lostitem::class);
        $this->security = $security;
    }

    /**
     * @return Lostitem[] Returns an array of Lostitem objects for the logged-in user
     */
    public function findByUser()
    {
        $user = $this->security->getUser();
        
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $user)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findByRoom(Rooms $room)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.room = :room')
            ->setParameter('room', $room)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Lostitem[] Returns an array of Lostitem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Lostitem
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
