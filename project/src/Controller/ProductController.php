<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/add", name="product_add", methods={"POST"})
     */
    public function add(Request $request, ProductRepository $productRepository): Response
    {
        $name = $request->request->get('name', '');
        $price = $request->request->get('price', 0);
        if (!$name) {
            return $this->json(['message'=>'name is empty'], Response::HTTP_BAD_REQUEST);
        }

        $product = $productRepository->add($name, $price);

        return $this->json([
            'message' => 'product added',
            'id' => $product->getId(),
        ]);
    }
}
