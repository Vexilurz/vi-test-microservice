<?php


namespace App\Service;


use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class OrderProductService
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(OrderRepository $orderRepository,
                                  ProductRepository $productRepository,
                                  UserRepository $userRepository,
                                  EntityManagerInterface $entityManager) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function updateProduct(Request $request): Response {
        $userFromRequest = $this->userRepository->getFromRequest($request);
        $order = $this->orderRepository->getFromRequest($request);
        $product = $this->productRepository->getFromRequest($request);

        $this->orderRepository->checkOrderBelongsToUser($order, $userFromRequest);

        $message = $this->orderProductAction($order, $product);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => $message
        ]);
    }

    abstract protected function orderProductAction(Order $order, Product $product): string;
}