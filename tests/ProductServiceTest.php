<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\ProductService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Product;
use App\Entity\Category;
use Psr\Log\LoggerInterface;

class ProductServiceTest extends KernelTestCase
{
    protected $productService;
    private $entityManager;
    private $productRepo;
    private $categoryRepo;

    /**
     * 
     */
    protected function setUp(): void
    { 
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //get logger mock object
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();        

        $this->purgeTable();        

        $this->productRepo  = $this->entityManager->getRepository(Product::class);
        $this->categoryRepo = $this->entityManager->getRepository(Category::class);
        $this->productService = new ProductService($this->entityManager, $this->productRepo, $this->categoryRepo, $logger);

        //seed categories for testing
        $categories = [
            'Games',
            'Computers',
            'TVs and Accessories'
        ];

        foreach($categories as $name)
        {
            $category = new Category();
            $category->setName($name);
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        //seed products for testing
        $products = [
            [
                'name'      => 'Pong',
                'category'  => 'Games',
                'sku'       => 'A0001',
                'price'     => 69.99,
                'quantity'  => 20
            
            ],
            [
                'name'      => 'GameStation 5',
                'category'  => 'Games',
                'sku'       => 'A0002',
                'price'     => 269.99,
                'quantity'  => 10
            
            ]
        ];

        foreach($products as $item)
        {
            $category = $this->categoryRepo->findOneByName($item['category']);
            $product = new Product();
            $product->setCategory($category);
            $product->setName($item['name']);
            $product->setPrice($item['price']);
            $product->setSKU($item['sku']);
            $product->setQuantity($item['quantity']);

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }

    }

    /* Tests */

    /**
     * 
     */
    public function test_getAllProducts()
    {
        $products = $this->productService->getAllProducts();

        $count = count($products);
        $this->assertEquals(2, $count);
    }

    /**
     * 
     */
    public function test_getProductById()
    {
        $id = 2;
        $product = $this->productService->getProductById($id);
        $this->assertEquals($id, $product['id']);
    }

    /**
     * 
     */
    public function test_addProduct()
    {
        $payload = [
            'name'      => 'Cards',
            'category'  => 'Gambling',
            'sku'       => 'A00019',
            'price'     => 19.99,
            'quantity'  => 10
        ];

        $id = $this->productService->addProduct($payload);
        //get newly added product
        $product = $this->productService->getProductById($id);
        $this->assertEquals($payload['name'], $product['name']);
    }

    /**
     * 
     */
    public function test_updateProduct()
    {
        $id = 2;
        $name = 'Dice';

        $payload = [
            'id'        => $id,
            'name'      => $name,
            //'category'  => 'Recreation',
            //'sku'       => 'A00019',
            //'price'     => 19.99,
            //'quantity'  => 10
        ];

        $prod_id = $this->productService->updateProduct($payload);
        //get newly added product
        $product = $this->productService->getProductById($prod_id);
        $this->assertEquals($payload['name'], $product['name']);
    }

    /**
     * 
     */
    public function test_deleteProduct()
    {
        $id = 2;
        $this->productService->deleteProduct($id);

        $product = $this->productRepo->find($id);
        $this->assertNull($product);
    }


    /* End Tests */

    /**
     * 
     */
    private function purgeTable()
    {
        $tables = [
            'category',
            'product',
        ];
        
        $cnx = $this->entityManager->getConnection();
        $cnx->query('SET FOREIGN_KEY_CHECKS=0');
        foreach($tables as $tableName)
        {
            $cnx->query("TRUNCATE TABLE {$tableName}");
        }
        $cnx->query('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->purgeTable();    

        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}