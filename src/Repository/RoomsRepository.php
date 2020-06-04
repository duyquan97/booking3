<?php

namespace App\Repository;

use App\Entity\Prices;
use App\Entity\Rooms;
use App\Entity\Stocks;
use Cassandra\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rooms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rooms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rooms[]    findAll()
 * @method Rooms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rooms::class);
    }

     /**
      * @return Rooms[] Returns an array of Rooms objects
      */

    public function findBySearch(float $fromPrice, float $toPrice, $fromDate, $toDate)
    {
        $data = $this->createQueryBuilder('r')
                ->Join(Prices::class, 'p', Join::WITH, 'r.id = p.room')
                ->Join(Stocks::class, 's', Join::WITH, 'r.id = s.room');
        if (!empty($fromPrice)) {
            $data = $data->andWhere('p.price >= :fromPrice')
                        ->setParameter('fromPrice', $fromPrice);
        }
        if (!empty($toPrice)) {
            $data = $data->andWhere('p.price <= :toPrice')
                ->setParameter('toPrice', $toPrice);
        }
        if (!empty($fromDate)) {
            $data = $data->andWhere('p.fromDate <= :fromDate AND s.toDate >= :fromDate')
                        ->setParameter('fromDate',$fromDate);
        }
        if (!empty($toDate)) {
            $data = $data->andWhere('p.toDate <= :toDate AND s.fromDate < :toDate')
                ->setParameter('toDate',$toDate);
        }
        $data = $data
            ->andWhere('s.amount > 0')
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $data;
    }

    public function findById(int $id)
    {
        return $this->createQueryBuilder('r')
            ->Join(Prices::class, 'p', Join::WITH, 'r.id = p.room')
            ->Join(Stocks::class, 's', Join::WITH, 'r.id = s.room')
            ->andWhere('s.room = :id')
            ->andWhere('p.room = :id')
            ->setParameter('id',$id)
            ->andWhere('s.fromDate >= p.fromDate AND s.toDate <= p.toDate')
            ->select('r.id','s.fromDate','s.toDate','s.amount','p.price')
            ->orderBy('s.fromDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function checkStock(int $roomId, $fromDate, $toDate, $amount) {
        $data = $this->createQueryBuilder('r')
            ->Join(Prices::class, 'p', Join::WITH, 'r.id = p.room')
            ->Join(Stocks::class, 's', Join::WITH, 'r.id = s.room')
            ->andWhere('s.room = :roomId')
            ->andWhere('p.room = :roomId')
            ->setParameter('roomId', $roomId)
            ->andWhere('s.fromDate >= p.fromDate AND s.toDate <= p.toDate')
            ->andWhere('s.fromDate >= :fromDate AND s.toDate <= :toDate')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->select('s.id','p.price','s.amount')
            ->andWhere('s.amount >= :amount')
            ->setParameter('amount',$amount)
            ->getQuery()
            ->getResult()
            ;
        return $data;
    }


    /*
    public function findOneBySomeField($value): ?Rooms
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
