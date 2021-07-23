<?php

namespace App\Repository;

use App\Entity\Transportation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transportation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transportation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transportation[]    findAll()
 * @method Transportation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransPortationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transportation::class);
    }

    // /**
    //  * @return Transportation[] Returns an array of Transportation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transportation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
