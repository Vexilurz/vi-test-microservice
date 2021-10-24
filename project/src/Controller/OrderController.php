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
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository,
                                UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/create", name="order_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $user = $this->userRepository->getFromRequest($request);
        $order = $this->orderRepository->create($user);

        return $this->json([
            'message' => 'new order created',
            'id' => $order->getId()
        ]);
    }

    /**
     * @Route("/add_product", name="order_add_product", methods={"POST"})
     */
    public function addProduct(Request $request, OrderProductAddService $service): Response
    {
        return $service->updateProduct($request);
    }

    /**
     * @Route("/remove_product", name="order_remove_product", methods={"POST"})
     */
    public function removeProduct(Request $request, OrderProductRemoveService $service): Response
    {
        return $service->updateProduct($request);
    }

    /**
     * @Route("/pay", name="order_pay", methods={"POST"})
     */
    public function pay(Request $request): Response
    {
        $order = $this->orderRepository->getFromRequest($request);
        $userFromRequest = $this->userRepository->getFromRequest($request);

        $this->orderRepository->checkOrderBelongsToUser($order, $userFromRequest);

        $this->orderRepository->setPaid($order, true);

        return $this->json([
            'message' => 'order has been paid',
            'id' => $order->getId()
        ]);
    }
}
