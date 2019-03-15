<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Exception\ValidatorException;
use Psr\Log\LoggerInterface;

/**
 * Contains product related operations
 */
class ProductService 
{
    /**
     * holds product repo instance
     */
    private $productRepo;

    /**
     * holds category repo instance
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
     * 
     */
    public function __construct(EntityManagerInterface $entityManager, 
                                ProductRepository $productRepo, 
                                CategoryRepository $categoryRepo, 
                                LoggerInterface $logger
    )
    {
        $this->productRepo   = $productRepo;
        $this->categoryRepo  = $categoryRepo;
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
    }

    /**
     * @return [] of products
     */
    public function getAllProducts()
    {
        return $this->productRepo->findAllProducts();
    }

    /**
     * @param $id 
     * 
     * @return [] product details
     */
    public function getProductById($id)
    {
        return $this->productRepo->findProductById($id);
    }

    /**
     * @param [] $params - product details
     * 
     * @return int product id
     */
    public function addProduct(array $params)
    {
        //get category by name or create
        $category = $this->categoryRepo->findOneByNameOrCreate($params['category']);

        // add product
        try
        {
            $product = new Product();
            $product->setCategory($category);
            $product->setName($params['name']);
            $product->setPrice($params['price']);
            $product->setSku($params['sku']);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            return $product->getId();
        }
        catch(Exception $e)
        {
            $this->logger->error('Failed to add product.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * @param [] $params - products details 
     * 
     * @return int product id 
     */
    public function updateProduct($params)
    {
        $product = $this->productRepo->find($params['id']);
        if (!$product) 
        {
            $this->logger->error('Failed to update product. Product not found', [
                'product ID' => $params['id']
            ]);
            return null;
        }

        if(!empty($params['category']))
        {
            $category = $this->categoryRepo->findOneByNameOrCreate($params['category']);
            $product->setCategory($category);
        }
            
        $product->setName($params['name'] ?? $product->getName());
        $product->setPrice($params['price'] ?? $product->getPrice());
        $product->setSku($params['sku'] ?? $product->getSku());
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product->getId();
    }

    /**
     * @param int $id - product id
     * 
     * @return void
     */
    public function deleteProduct($id)
    {
        $product = $this->productRepo->find($id);
        if (!$product) 
        {
            $this->logger->error('Failed to delete product. Product not found', [
                'product ID' => $params['id']
            ]);
            return null;
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * Helper function to validate input
     */
    public function validate($value)
    {
        $constraints = new Collection([
            'name'     => [new Optional(new NotBlank())],
            'category' => [new Optional(new NotBlank())],
            'sku'      => [new Optional(new NotBlank())],
            'price'    => [new Optional(new NotBlank())],
        ]);

        $validator = Validation::createValidator();
        $violations = $validator->validate($value, $constraints);
        if (count($violations) > 0) 
        {
            return false;
        }

        return true;
    }

}