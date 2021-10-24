<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\OrderProductAddService;
use App\Service\OrderProductRemoveService;
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
     * @Route("/create", name="order_create", methods={"POST"})
     */
    public function create(Request $request, OrderRepository $orderRepository,
                           UserRepository $userRepository): Response
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $user = $userRepository->findOneBy(['apiToken' => $apiToken]);
        $order = $orderRepository->create($user);

        return $this->json([
            'message' => 'new order created',
            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/add_product", name="order_add_product", methods={"POST"})
     */
    public function addProduct(Request $request, OrderProductAddService $orderService): Response
    {
        return $orderService->updateProduct($request);
    }

    /**
     * @Route("/remove_product", name="order_remove_product", methods={"POST"})
     */
    public function removeProduct(Request $request, OrderProductRemoveService $orderService): Response
    {
        return $orderService->updateProduct($request);
    }
}
