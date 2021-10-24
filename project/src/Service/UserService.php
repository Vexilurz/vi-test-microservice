<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserService
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository,
                                UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getFromRequest(Request $request): User
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $user = $this->userRepository->findOneBy(['apiToken' => $apiToken]);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function getSerializedOrders(Request $request, $userId = null): array
    {
        if ($userId) {
            $user = $this->userRepository->find($userId);
            if (!$user) {
                throw new NotFoundHttpException('user not found');
            }
        } else {
            $user = $this->getFromRequest($request);
        }

        $onlyPaid = $request->query->get('paid');
        $orders = $onlyPaid ? $this->orderRepository->findPaidUserOrders($user) : $user->getOrders();

        $ordersSerialized = [];
        foreach ($orders as $order) {
            $ordersSerialized[] = $order->getSerialized();
        }
        return $ordersSerialized;
    }
}