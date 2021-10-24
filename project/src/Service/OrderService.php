<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Utils\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderService
{
    private OrderRepository $orderRepository;
    private UserService $userService;

    public function __construct(OrderRepository $orderRepository,
                                UserService $userService) {
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
    }

    public function getFromRequest(Request $request, $checkOwner = true): Order {
        $orderId = $request->request->get('orderId', 0);
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException('order not found');
        }
        if ($checkOwner) {
            $userFromRequest = $this->userService->getFromRequest($request);
            if ($order->getUser() !== $userFromRequest) {
                throw new AccessDeniedHttpException('user is not the owner of the order');
            }
        }
        return $order;
    }

    public function create(Request $request): Order {
        $user = $this->userService->getFromRequest($request);
        return $this->orderRepository->create($user);
    }

    public function setPaid(Order $order, bool $paid): Order {
        return $this->orderRepository->setPaid($order, $paid);
    }

    public function getSerializedOrders(Request $request)
    {
        $fromDate = $request->query->get('fromDate');
        $toDate = $request->query->get('toDate');
        $fromDate = $fromDate ? new \DateTimeImmutable($fromDate) : (new \DateTimeImmutable())->setTimestamp(0);
        $toDate = $toDate ? new \DateTimeImmutable($toDate) : (new \DateTimeImmutable('now'));
        $orders = $this->orderRepository->findOrdersByDate($fromDate, $toDate);

        return Serializer::getSerializedFromArray($orders, ['includeUser'=>true]);
    }
}