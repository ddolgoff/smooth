<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return array[] Returns an array of Categories data
     */
    public function findAllCategories()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.name')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array[] Returns an array of Categories data
     */
    public function findCategory($id)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.name')
            ->orderBy('c.id', 'ASC')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array[] Returns an array of Categories entities
     */
    public function findOneByNameOrCreate(string $name)
    {
        $entity = $this->findOneByName($name);

        if (null === $entity)
        {
           $entity = new Category();
           $entity->setName($name); 
           $this->_em->persist($entity);
           $this->_em->flush();
        }

        return $entity;
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
