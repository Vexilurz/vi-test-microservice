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
    private UserService $userService;
    protected OrderRepository $orderRepository;

    public function __construct(OrderService $orderService,
                                ProductService $productService,
                                 UserService $userService,
                                OrderRepository $orderRepository) {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->userService = $userService;
        $this->orderRepository = $orderRepository;
    }

    public function updateProduct(Request $request): string {
        $userFromRequest = $this->userService->getFromRequest($request);
        $order = $this->orderService->getFromRequest($request);
        $product = $this->productService->getFromRequest($request);

        $this->orderService->checkOrderBelongsToUser($order, $userFromRequest);

        return $this->orderProductAction($order, $product);
    }

    abstract protected function orderProductAction(Order $order, Product $product): string;
}