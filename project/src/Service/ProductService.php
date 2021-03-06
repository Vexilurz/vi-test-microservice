<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Utils\JsonConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;

    public function __construct(
        ProductRepository $productRepository,
        OrderRepository   $orderRepository
    ) {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    public function add(Request $request): Product
    {
        $name = $request->request->get('name', '');
        $price = $request->request->get('price', 0);
        if (!$name) {
            throw new BadRequestHttpException('name is empty');
        }

        return $this->productRepository->add($name, $price);
    }

    public function getFromRequest(Request $request): Product
    {
        $productId = $request->request->get('productId', 0);
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new NotFoundHttpException('product not found');
        }

        return $product;
    }

    public function getSerializedProductsFromOrder(Request $request, $orderId): array
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException('order not found');
        }

        $available = $request->query->get('available');
        $products = $available ?
            $this->productRepository->findAvailableInOrder($order) :
            $order->getOrderProducts()->getValues();

        return JsonConverter::getJsonFromEntitiesArray($products);
    }
}