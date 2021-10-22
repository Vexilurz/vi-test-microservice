<?php


namespace App\Service;


use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderService
{
    const PRODUCT_ADD = 'product_add';
    const PRODUCT_REMOVE = 'product_remove';

    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __constructor(OrderRepository $orderRepository,
                                  ProductRepository $productRepository,
                                  UserRepository $userRepository,
                                  EntityManagerInterface $entityManager) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function updateProduct(Request $request, string $kind): Response {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $userFromToken = $this->userRepository->findOneBy(['apiToken' => $apiToken]);

        $orderId = $request->request->get('orderId', 0);
        $productId = $request->request->get('productId', 0);
        $order = $this->orderRepository->find($orderId);
        $product = $this->productRepository->find($productId);

        if (!$order || !$product) {
            return new JsonResponse(['message'=>'order or product not found'],
                Response::HTTP_BAD_REQUEST);
        }

        if ($order->getUser() !== $userFromToken) {
            return new JsonResponse(
                ['message'=>'user from apiToken are not the owner of that order'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($kind === self::PRODUCT_ADD) {$order->addProduct($product);}
        if ($kind === self::PRODUCT_REMOVE) {$order->removeProduct($product);}
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'product added to order'
        ]);
    }
}