<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Guests;
use App\Entity\Prices;
use App\Entity\Rooms;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

     /**
      * @return Booking[] Returns an array of Booking objects
      */

    public function getBooking()
    {
        return $this->createQueryBuilder('b')
            ->Join(Rooms::class, 'r', Join::WITH, 'r.id = b.room')
//            ->Join(Guests::class, 'g', Join::WITH, 'g.id = b.guest')
            ->Join(User::class, 'u', Join::WITH, 'u.id = b.user')
            ->select('b.id', 'b.code', 'b.price', 'b.fromDate', 'b.toDate', 'b.amount', 'b.accept', 'r.id as room_id', 'u.id as user_id')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
