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

    public function __construct(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function getFromRequest(Request $request): Order {
        $orderId = $request->request->get('orderId', 0);
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException('order not found');
        }
        return $order;
    }

    public function checkOrderBelongsToUser(Order $order, User $user): bool {
        if ($order->getUser() !== $user) {
            throw new AccessDeniedHttpException('user is not the owner of the order');
        }
        return true;
    }

    public function create(User $user): Order {
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