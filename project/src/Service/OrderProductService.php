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
        $userFromRequest = $this->userRepository->getUserFromRequest($request);

        $orderId = $request->request->get('orderId', 0);
        $productId = $request->request->get('productId', 0);
        $order = $this->orderRepository->find($orderId);
        $product = $this->productRepository->find($productId);

        if (!$order || !$product) {
            return new JsonResponse(['message'=>'order or product not found'],
                Response::HTTP_BAD_REQUEST);
        }

        if ($order->getUser() !== $userFromRequest) {
            return new JsonResponse(
                ['message'=>'user from apiToken are not the owner of that order'],
                Response::HTTP_FORBIDDEN
            );
        }

        $message = $this->orderProductAction($order, $product);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => $message
        ]);
    }

    abstract protected function orderProductAction(Order $order, Product $product): string;
}