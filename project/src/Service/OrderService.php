<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Utils\JsonConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderService
{
    private OrderRepository $orderRepository;
    private UserService $userService;
    private ProductService $productService;

    public function __construct(OrderRepository $orderRepository,
                                UserService $userService,
                                ProductService $productService
    ) {
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
        $this->productService = $productService;
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

    public function delete(Request $request): void
    {
        $order = $this->getFromRequest($request);
        $this->orderRepository->delete($order);
    }

    public function getSerializedOrders(Request $request): array
    {
        $fromDate = $request->query->get('fromDate');
        $toDate = $request->query->get('toDate');
        $fromDate = $fromDate ? new \DateTimeImmutable($fromDate) : (new \DateTimeImmutable())->setTimestamp(0);
        $toDate = $toDate ? new \DateTimeImmutable($toDate) : (new \DateTimeImmutable('now'));
        $orders = $this->orderRepository->findOrdersByDate($fromDate, $toDate);

        return JsonConverter::getJsonFromEntitiesArray($orders, ['includeUser'=>true]);
    }

    public function removeProduct(Request $request): Order
    {
        $order = $this->getFromRequest($request);
        $product = $this->productService->getFromRequest($request);
        //TODO: check if product available in order?
        return $this->orderRepository->removeProduct($order, $product);
    }

    public function addProduct(Request $request): Order
    {
        $order = $this->getFromRequest($request);
        $product = $this->productService->getFromRequest($request);
        return $this->orderRepository->addProduct($order, $product);
    }
}