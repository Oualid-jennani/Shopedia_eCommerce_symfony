<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findStore()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s')
            ->where('s.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_ADMIN%');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findUsersByRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }


    /**
     * @param $role
     *
     * @return int|mixed|string
     */
    public function QueryUsersByRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        return  $qb->select('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');
    }

    /**
     * @param City $city
     * @return int|mixed|string
     */
    public function findSearchByCity(City $city){

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.city = :city')->setParameter('city',$city)
            ->andWhere('r.roles LIKE :roles')->setParameter('roles','%"ROLE_RESTAURANT"%')
            ->join('r.sponsoredRestaurant','sponsoredRestaurant')
        ;
        return $qb->getQuery()->getResult();
    }

    /**
     * @return int|mixed|string   Return default restaurant sponsored
     */
    public function findRestaurantSponsored()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_RESTAURANT"%')
            ->join('r.sponsoredRestaurant','sponsoredRestaurant')
        ;
        return $qb->getQuery()->getResult();
    }

    public  function findRestaurantByName($name) {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_RESTAURANT"%')
            ->andWhere('r.storeName LIKE :name')->setParameter('name',$name.'%')
        ;
        return $qb->getQuery()->getResult();
    }

    public  function findChefByName($name) {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->where('c.roles LIKE :roles')->setParameter('roles','%"ROLE_CHEF"%')
            ->andWhere('c.storeName LIKE :name')->setParameter('name',$name.'%')
        ;
        return $qb->getQuery()->getResult();
    }

    public  function findRestaurantByCity($city) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_RESTAURANT"%')
            ->andWhere('r.city = :city')->setParameter('city',$city)
        ;

        return $qb->getQuery()->getResult();
    }

    public  function findChefByCity($city) {

        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->where('c.roles LIKE :roles')->setParameter('roles','%"ROLE_CHEF"%')
            ->andWhere('c.city = :city')->setParameter('city',$city)
        ;

        return $qb->getQuery()->getResult();
    }

    public  function findRestaurantByCountry($country) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->join('r.city','c')
            ->join('c.country','cn')
            ->where('cn = :country')
            ->setParameter('country', $country)
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_RESTAURANT"%')
        ;

        return $qb->getQuery()->getResult();
    }

    public  function findAllDrivers() {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_DRIVER"%')
            ->andWhere('r.status = :stat')->setParameter('stat','active')
        ;
        return $qb->getQuery()->getResult();
    }
    public  function findDriversByCity($city) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_DRIVER"%')
            ->andWhere('r.city = :city')->setParameter('city',$city)
            ->andWhere('r.status = :stat')->setParameter('stat','active')
        ;
        return $qb->getQuery()->getResult();
    }
    public  function findDriversByCountry($country) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->join('r.city','c')
            ->join('c.country','cn')
            ->where('cn = :country')
            ->setParameter('country', $country)
            ->andWhere('r.roles LIKE :roles')->setParameter('roles','%"ROLE_DRIVER"%')
            ->andWhere('r.status = :stat')->setParameter('stat','active')
        ;
        return $qb->getQuery()->getResult();
    }

    public  function findDriversById($id) {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_DRIVER"%')
            ->andWhere('r.id = :id')->setParameter('id',$id)
        ;
        return $qb->getQuery()->getOneOrNullResult();
    }
    public  function findDriversPendingByCity($city) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_DRIVER"%')
            ->andWhere('r.city = :city')->setParameter('city',$city)
            ->andWhere('r.status = :stat')->setParameter('stat','pending')
        ;
        return $qb->getQuery()->getResult();
    }

    public  function findDriversOnlineByCity($city) {

        $qb = $this->createQueryBuilder('d');
        $qb->select('d')
            ->where('d.roles LIKE :roles')->setParameter('roles','%"ROLE_DRIVER"%')
            ->andWhere('d.city = :city')->setParameter('city',$city)
            ->andWhere('d.occupied = false')
        ;

        return $qb->getQuery()->getResult();
    }

    public  function findSecondAdminByCity($city) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_SECOND_ADMIN"%')
            ->andWhere('r.city = :city')->setParameter('city',$city)
        ;

        return $qb->getQuery()->getResult();
    }
    public  function findSecondAdminOneByCity($city) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_SECOND_ADMIN"%')
            ->andWhere('r.city = :city')->setParameter('city',$city)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
    public  function findSecondAdminByCountry($country) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->join('r.city','c')
            ->join('c.country','cn')
            ->where('cn = :country')
            ->setParameter('country', $country)
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_SECOND_ADMIN"%')
        ;

        return $qb->getQuery()->getResult();
    }

    public  function findCustomerByCity($city) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_CUSTOMER"%')
            ->andWhere('r.city = :city')->setParameter('city',$city)
        ;

        return $qb->getQuery()->getResult();
    }
    public  function findSCustomerByCountry($country) {

        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->join('r.city','c')
            ->join('c.country','cn')
            ->where('cn = :country')
            ->setParameter('country', $country)
            ->where('r.roles LIKE :roles')->setParameter('roles','%"ROLE_CUSTOMER"%')
        ;

        return $qb->getQuery()->getResult();
    }

}
