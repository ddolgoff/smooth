<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\CategoryService;
use App\Service\ProductService;
//use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\CategoryType;
use Psr\Log\LoggerInterface;

class ApiControllerTest extends WebTestCase
{
    private $entityManager;
    protected $productService;
    private $productRepo;
    private $categoryRepo;

    //private $apiToken = 'ztoken';

    protected function setUp(): void
    { 
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();
        $this->entityManager = $container
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
            'Milk',
            'Cheese',
            'Bread'
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
                'name'      => 'Skim milk',
                'category'  => 'Milk',
                'sku'       => 'A0001',
                'price'     => 69.99,
            
            ],
            [
                'name'      => 'Blue cheese',
                'category'  => 'Cheese',
                'sku'       => 'A0002',
                'price'     => 269.99,
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

            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }    

    /***************************
     * product api test cases
     ***************************/

    public function test_products()
    {
        $client = static::createClient();

        $client->request('GET', '/api/products');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_delete_product()
    {
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/product/1',
            [],
            [],
            []
        );
        //204 = No Content
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }   

    public function test_post_product()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/product',
            [],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json'],
            '{"name": "Swiss cheese","category": "Cheese","sku": "A0003","price": 1399.99}'
        );
        //201 = Created
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }   
    
    public function test_put_product()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/product/1',
            [],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json'],
            '{"name": "item UPDATED","category": "Milk","sku": "A0003","price": 399.99}'
        );
        //201 = Created
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }   
    
    
    /***************************
     * category api test cases
     ***************************/

    public function test_category_get_method()
    {
        $client = static::createClient();

        $client->request('GET', '/api/categories');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function test_category_post()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/category',
            [],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json'],
            '{"name": "Test Category"}'
        );
        //201 = Created
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    } 
    
    public function test_category_put_method()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/category/1',
            [],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json', 'HTTP_X-AUTH-TOKEN' => $this->apiToken],
            '{"name": "Update Name"}'
        );
        //201 = Created
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }   

    //End Tests

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

    protected function tearDown()
    {
        //$this->purgeTable();    
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}