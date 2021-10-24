<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository,
                                UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/get_orders", name="user_get_orders", methods={"GET"})
     */
    public function getOrders(Request $request): Response
    {
        $user = $this->userRepository->getFromRequest($request);

        $onlyPaid = $request->query->get('paid');
        $orders = $onlyPaid ? $this->orderRepository->findPaidUserOrders($user) : $user->getOrders();

        $ordersSerialized = $this->userRepository->getOrdersSerialized($orders);

        return $this->json($ordersSerialized);
    }

    /**
     * @Route("/get_orders/{userId}", name="user_get_orders_by_userid", methods={"GET"})
     */
    public function getOrdersByUserId(Request $request, $userId): Response
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new NotFoundHttpException('user not found');
        }

        $onlyPaid = $request->query->get('paid');
        $orders = $onlyPaid ? $this->orderRepository->findPaidUserOrders($user) : $user->getOrders();

        $ordersSerialized = $this->userRepository->getOrdersSerialized($orders);

        return $this->json($ordersSerialized);
    }
}
