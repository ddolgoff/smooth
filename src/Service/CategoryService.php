<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use Psr\Log\LoggerInterface;

/**
 * Contains category related operations
 */
class CategoryService
{
    /**
     * holds categor repo instance
     */
    private $categoryRepo;

    /**
     * holds entity manager instance
     */
    private $entityManager;

    /**
     * holds logger instance
     */
    private $logger;

    /**
     * object contructor
     */
    public function __construct(EntityManagerInterface $entityManager, 
                                CategoryRepository $categoryRepo, 
                                LoggerInterface $logger
    )
    {
        $this->categoryRepo  = $categoryRepo;
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
    }

    /**
     * @return [] categories and related products
     */
    public function getAllCategories()
    {
        return $this->categoryRepo->findAllCategories();
    }

    /**
     * @param int $id - category id
     * 
     * @return [] category details and related products
     */
    public function getCategoryById($id)
    {
        return $this->categoryRepo->findCategory($id);
    }

    /**
     * @param [] $params - category details
     * 
     * @return int category id
     */
    public function addCategory($params)
    {
        try
        {
            $category = new Category();
            $category->setName($params['name']);
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            return $category->getId();
        }
        catch(Exception $e)
        {
            $this->logger->error('Failed to add category.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * @param [] $params - category details
     * 
     * @return int category id
     */
    public function updateCategory($params)
    {
        $category = $this->categoryRepo->find($params['id']);
        if (!$category) 
        {
            $this->logger->error('Failed to update category. Category not found', [
                'product ID' => $params['id']
            ]);
            return null;
        }

        $category->setName($params['name']);
        $this->entityManager->flush();
        return $category->getId();
    }

    /**
     * @param int $id - category id
     * 
     * @return void
     */
    public function deleteCategory($id)
    {
        $category = $this->categoryRepo->find($id);
        if (!$category) 
        {
            $this->logger->error('Failed to delete category. Category not found', [
                'product ID' => $params['id']
            ]);
            return null;
        }
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

}