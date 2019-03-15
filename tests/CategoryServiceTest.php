<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Category;

class CategoryServiceTest extends KernelTestCase
{
    private $entityManager;
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

        $this->categoryRepo = $this->entityManager->getRepository(Category::class);
        $this->categoryService = new CategoryService($this->entityManager, $this->categoryRepo, $logger);

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

    }

    /* Tests */

    /**
     * 
     */
    public function test_getAllCategories()
    {
        $categories = $this->categoryService->getAllCategories();

        $count = count($categories);
        $this->assertEquals(3, $count);
    }

    /**
     * 
     */
    public function test_getCategoryById()
    {
        $id = 2;
        $category = $this->categoryService->getCategoryById($id);
        $this->assertEquals($id, $category->getId());
    }

    /**
     * 
     */
    public function test_addCategory()
    {
        $payload = [
            'name'      => 'Gambling',
        ];

        $id = $this->categoryService->addCategory($payload);
        //get newly added category
        $category = $this->categoryService->getCategoryById($id);
        $this->assertEquals($payload['name'], $category->getName());
    }

    /**
     * 
     */
    public function test_updateCategory()
    {
        $id = 2;
        $name = 'Cars';

        $payload = [
            'id'        => $id,
            'name'      => $name,
        ];

        $cat_id = $this->categoryService->updateCategory($payload);
        //get newly added category
        $category = $this->categoryService->getCategoryById($cat_id);
        $this->assertEquals($payload['name'], $category->getName());
    }

    /**
     * 
     */
    public function test_deleteCategory()
    {
        $id = 2;
        $this->categoryService->deleteCategory($id);

        $category = $this->categoryRepo->find($id);
        $this->assertNull($category);
    }


    /* End Tests */

    /**
     * 
     */
    private function purgeTable()
    {
        $tables = [
            'category',
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