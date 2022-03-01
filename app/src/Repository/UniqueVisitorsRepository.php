<?php

namespace App\Repository;

use App\Entity\UniqueVisitors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UniqueVisitors|null find($id, $lockMode = null, $lockVersion = null)
 * @method UniqueVisitors|null findOneBy(array $criteria, array $orderBy = null)
 * @method UniqueVisitors[]    findAll()
 * @method UniqueVisitors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniqueVisitorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UniqueVisitors::class);
    }

    // /**
    //  * @return UniqueVisitors[] Returns an array of UniqueVisitors objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UniqueVisitors
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
