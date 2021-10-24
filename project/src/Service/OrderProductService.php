<?php


namespace App\Service;


use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Request;

abstract class OrderProductService
{
    private OrderService $orderService;
    private ProductService $productService;
    protected OrderRepository $orderRepository;

    public function __construct(OrderService $orderService,
                                ProductService $productService,
                                OrderRepository $orderRepository) {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
    }

    public function updateProduct(Request $request): string {
        $order = $this->orderService->getFromRequest($request);
        $product = $this->productService->getFromRequest($request);

        return $this->orderProductAction($order, $product);
    }

    abstract protected function orderProductAction(Order $order, Product $product): string;
}