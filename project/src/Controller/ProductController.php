<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="product")
 */
class ProductController extends AbstractController
{
    private ProductService $service;

    public function __construct(ProductService $service) {
        $this->service = $service;
    }

    /**
     * @Route("/add", name="product_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $product = $this->service->add($request);

        return $this->json([
            'message' => 'product added',
            'id' => $product->getId(),
        ]);
    }

    /**
     * @Route("/get_from_order/{orderId}", name="product_get_from_order", methods={"GET"})
     */
    public function getFromOrder(Request $request, $orderId): Response
    {
        try {
            $productsSerialized = $this->service->getSerializedProductsFromOrder($request, $orderId);
        } catch(HttpException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->json($productsSerialized);
    }
}
