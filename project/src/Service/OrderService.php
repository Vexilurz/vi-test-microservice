<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Payment\PaymentService;
use App\Utils\JsonConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderService
{
    private OrderRepository $orderRepository;
    private UserService $userService;
    private ProductService $productService;
    private PaymentService $paymentService;

    public function __construct(OrderRepository $orderRepository,
                                UserService $userService,
                                ProductService $productService,
                                PaymentService $paymentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
        $this->productService = $productService;
        $this->paymentService = $paymentService;
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

    public function payOrder(Order $order): Order {
        if ($order->getPaid())
        {
            throw new BadRequestException('order is paid already');
        }
        $method = $this->paymentService->getPaymentMethod();
        $paymentResult = $method->payOrder($order);
        //TODO: process if (!$paymentResult)
        return $this->orderRepository->setPaid($order, $paymentResult);
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