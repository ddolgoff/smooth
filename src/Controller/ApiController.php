<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
//use App\Form\ProductType;
use App\Service\ProductService;
use App\Service\CategoryService;

/**
 * API controller.
 * @Route("/api", name="api")
 */
class ApiController extends AbstractFOSRestController
{
  /**
   * holds productService instance
   */
  private $productService;

  /**
   * holds categoryService instance
   */
  private $categoryService;

  public function __construct(ProductService $productService, CategoryService $categoryService)
  {
      $this->productService  = $productService;
      $this->categoryService = $categoryService;
  }

  /**********************
   * Product actions
   *********************/

  /**
   * Lists all products.
   * @Rest\Get("/products")
   *
   * @return Response
   */
  public function getProductsAction()
  {
    $products = $this->productService->getAllProducts();
    return $this->handleView($this->view($products));
  }

  /**
   * Get product by ID.
   * @Rest\Get("/product/{id}")
   *
   *  @return Response
   */
  public function getProductAction(int $id)
  {
    $product = $this->productService->getProductById($id);
    return $this->handleView($this->view($product));
  }

  /**
   * Add product
   * @Rest\Post("/product")
   *    
   * Exptects the following input: {'name': 'name here', 'category': 'category name here', 'sku': 'sku here', 'price': float value}
   * 
   * @return Response
   */
  public function postProductAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);
    if($this->productService->validate($data))
    {
      $id = $this->productService->addProduct($data);
      return $this->handleView($this->view(['status' => 'ok','id' => $id], Response::HTTP_CREATED));
    }  

    return $this->handleView($this->view('Malformed payload', Response::HTTP_BAD_REQUEST));
  }

  /**
   * Update product
   * @Rest\Put("/product/{id}")
   *    
   * Exptects the following input: {'name': 'name here', 'category': 'category name here', 'sku': 'sku here', 'price': float value}
   * 
   * @return Response
   */
  public function putProductAction(int $id, Request $request)
  {
    $data = json_decode($request->getContent(), true);
    if($this->productService->validate($data))
    {
      $data['id'] = $id;
      $prod_id = $this->productService->updateProduct($data);
      if($prod_id)
      {
        return $this->handleView($this->view(['status' => 'ok','id' => $prod_id], Response::HTTP_CREATED));
      }
    }  

    return $this->handleView($this->view('Malformed payload or wrong product id', Response::HTTP_BAD_REQUEST));
  }

  /**
   * Delete product
   * @Rest\Delete("/product/{id}")
   * 
   * @return Response
   */
  public function deleteProductAction(int $id)
  {
    $this->productService->deleteProduct($id);
    return $this->handleView($this->view([], Response::HTTP_NO_CONTENT));
  }


  /**********************
   * Category actions
   *********************/

  /**
   * Lists all categories.
   * @Rest\Get("/categories")
   *
   * @return Response
   */
  public function getCategoriesAction()
  {
    $categories = $this->categoryService->getAllCategories();
    return $this->handleView($this->view($categories));
  }

  /**
   * Get category by ID
   * @Rest\Get("/category/{id}")
   *
   * @return Response
   */
  public function getCategoryAction(int $id)
  {
    $category = $this->categoryService->getCategoryById($id);
    return $this->handleView($this->view($category));
  }

  /**
   * Add category
   * @Rest\Post("/category")
   *    
   * Exptects the following input: {'name': 'category name here'}
   * 
   * @return Response
   */
  public function postCategoryAction(Request $request)
  {
    $category = new Category();
    $form = $this->createForm(CategoryType::class, $category);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    
    if ($form->isSubmitted() && $form->isValid()) 
    {
      $id = $this->categoryService->addCategory($data);
      return $this->handleView($this->view(['status' => 'ok','id' => $id], Response::HTTP_CREATED));
    }

    return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
  }

  /**
   * Update category
   * @Rest\Put("/category/{id}")
   *    
   * Exptects the following input: {'name': 'category name here'}
   * 
   * @return Response
   */
  public function putCategoryAction(int $id, Request $request)
  {
    $category = new Category();
    $form = $this->createForm(CategoryType::class, $category);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);

    if ($form->isSubmitted() && $form->isValid()) 
    {
      $data['id'] = $id;
      $id = $this->categoryService->updateCategory($data);
      return $this->handleView($this->view(['status' => 'ok','id' => $id], Response::HTTP_CREATED));
    }

    return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
  }

  /**
   * Delete category
   * @Rest\Delete("/category/{id}")
   * 
   * @return Response
   */
  public function deleteCategoryAction(int $id)
  {
    $this->categoryService->deleteCategory($id);
    return $this->handleView($this->view([], Response::HTTP_NO_CONTENT));
  }

}
