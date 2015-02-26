<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
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
}
