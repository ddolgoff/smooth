<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return array[] Returns an array of Products data
     */
    public function findAllProducts()
    {
        return $this->createQueryBuilder('p')
        ->select('p.id', 'c.name AS category', 'p.name', 'p.sku', 'p.price')
        ->innerJoin('p.category', 'c')
        ->getQuery()
        ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @return array[] Returns an array of Product details
     */
    public function findProductById($id)
    {
        return $this->createQueryBuilder('p')
        ->select('p.id','c.name AS category', 'p.name', 'p.sku', 'p.price')
        ->innerJoin('p.category', 'c')
        ->andWhere('p.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
   

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
