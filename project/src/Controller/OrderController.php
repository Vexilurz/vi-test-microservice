<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/create", name="order_create")
     */
    public function create(Request $request, OrderRepository $orderRepository,
                           UserRepository $userRepository): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->json(['message'=>'Must be a POST method'], Response::HTTP_BAD_REQUEST);
        }

        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $user = $userRepository->findOneBy(['apiToken' => $apiToken]);
        $order = $orderRepository->create($user);

        return $this->json([
            'message' => 'new order created',
            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/add_product", name="order_add_product")
     */
    public function addProduct(Request $request, OrderRepository $orderRepository,
                               ProductRepository $productRepository,
                               UserRepository $userRepository,
                               EntityManagerInterface $entityManager): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->json(['message'=>'Must be a POST method'], Response::HTTP_BAD_REQUEST);
        }

        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $userFromToken = $userRepository->findOneBy(['apiToken' => $apiToken]);

        $orderId = $request->request->get('orderId', 0);
        $productId = $request->request->get('productId', 0);
        $order = $orderRepository->find($orderId);
        $product = $productRepository->find($productId);

        if (!$order || !$product) {
            return $this->json(['message'=>'order or product not found'], Response::HTTP_BAD_REQUEST);
        }

        if ($order->getUser() !== $userFromToken) {
            return $this->json(
                ['message'=>'user from apiToken are not the owner of that order'],
                Response::HTTP_FORBIDDEN
            );
        }

        $order->addProduct($product);
        $entityManager->flush();

        return $this->json([
            'message' => 'product added to order'
        ]);
    }
}
