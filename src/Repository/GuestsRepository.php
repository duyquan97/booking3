<?php

namespace App\Repository;

use App\Entity\Guests;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Guests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guests[]    findAll()
 * @method Guests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guests::class);
    }

     /**
      * @return Guests[] Returns an array of Guests objects
      */

    public function getGuests()
    {
        return $this->createQueryBuilder('g')
            ->Join(User::class, 'u', Join::WITH, 'u.id = g.user')
            ->orderBy('g.id', 'ASC')
            ->select('g.id','g.name','g.phone','g.email','u.id as user_id')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function listGuestBooking ($guest) {
        return $this->createQueryBuilder('g')
            ->andWhere('g.id IN :array')
            ->setParameter('array',$guest)
            ->orderBy('g.id', 'ASC')
            ->select('g.id','g.name','g.phone','g.email')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Guests
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
