<?php

namespace App\Repository;

use App\Entity\SponsoredRestaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SponsoredRestaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method SponsoredRestaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method SponsoredRestaurant[]    findAll()
 * @method SponsoredRestaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsoredRestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SponsoredRestaurant::class);
    }

    // /**
    //  * @return SponsoredRestaurant[] Returns an array of SponsoredRestaurant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SponsoredRestaurant
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
