<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

use ApiBundle\Entity\Product;
use ApiBundle\Form\ProductType;

class ApiController extends Controller
{
    /*
     * Get products list
     */
    public function getProductsAction()
    {
        $productsFromDoctrine = $this->getDoctrine()
                ->getRepository('ApiBundle:Product')->findAll();
        $products = array();
        foreach ($productsFromDoctrine as $product)
        {
            $products[$product->getId()] = array(
                "id" => $product->getId(),
                "title" => $product->getTitle(),
                "description" => $product->getDescription(),
                "photo" => $product->getPhoto()
            );
        }
        
        return new JsonResponse($products);
    }
    
    /*
     * Get product entities
     */
    public function getProductAction($id)
    {
        $productFromDoctrine = $this->getDoctrine()
                ->getRepository('ApiBundle:Product')->find($id);
        
        if(!$productFromDoctrine instanceof Product){
            throw new NotFoundHttpException('Product not found');
        }
        
        $product = array(
            "id" => $productFromDoctrine->getId(),
            "title" => $productFromDoctrine->getTitle(),
            "description" => $productFromDoctrine->getDescription(),
            "photo" => $productFromDoctrine->getPhoto()
        );
        
        return new JsonResponse($product);
        
    }
    
    /*
     * Create product entity
     */
    public function postProductAction()
    {
        return $this->processForm(new Product());
    }
    
    /*
     * Update product entity
     */
    public function putProductAction($id)
    {
        $product = new Product();
        $product->setId($id);
        return $this->processForm($product);
    }
    
    /*
     * Delete product
     */
    public function deleteProductAction($id)
    {
        $this->deleteProduct($id);
        $response = new Response();   
        $response->setStatusCode(200);
        return $response;
    }
    
    private function processForm(Product $product)
    {
        $statusCode = $product->isNew() ? 201 : 204;
        $request = $this->getRequest();
        $data = json_decode($request->getContent(), true);
        $request->request->replace($data);
        $product->setTitle($request->request->get('title'))
                ->setDescription($request->request->get('description'))
                ->setPhoto($request->request->get('photo', ''));
        if($product->isNew())
        {
           $this->createProduct($product);
        }
        else{
            $this->updateProduct($product);
        }
        $response = new Response();   
        $response->setStatusCode($statusCode);
        return $response;
    }
    
    private function createProduct(Product $product)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($product);
        $em->flush();
    }
    
    private function updateProduct(Product $product)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $productFromDoctrine = $em->getRepository('ApiBundle:Product')->find($product->getId());
        if(!$productFromDoctrine)
        {
            throw $this->createNotFoundException('No product found for id '.$product->getId());
        }
        
        $productFromDoctrine->setTitle($product->getTitle());
        $productFromDoctrine->setDescription($product->getDescription());
        $productFromDoctrine->setPhoto($product->getPhoto());
        $em->flush();
    }
    
    private function deleteProduct($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $productFromDoctrine = $em->getRepository('ApiBundle:Product')->find($id);
        if(!$productFromDoctrine)
        {
            throw $this->createNotFoundException('No product found for id '.$id);
        }
        
        $em->remove($productFromDoctrine);
        $em->flush();
    }
}
