<?php


namespace App\Service\Payment;


use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Payment\Strategy\PaymentStrategyInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractPaymentService
{
    private OrderRepository $orderRepository;
    private PaymentStrategyInterface $paymentStrategy;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    protected function getPaymentStrategy(): PaymentStrategyInterface
    {
        if (!$this->paymentStrategy) {
            $this->paymentStrategy = $this->getNewPaymentStrategy();
        }
        return $this->paymentStrategy;
    }

    abstract protected function getNewPaymentStrategy(): PaymentStrategyInterface;

    public function payOrder(Order $order): Order {
        if ($order->getPaid())
        {
            throw new BadRequestHttpException('order is paid already');
        }
        $paymentStrategy = $this->getPaymentStrategy();
        $paymentResult = $paymentStrategy->pay($order);
        //TODO: process if (!$paymentResult)
        return $this->orderRepository->setPaid($order, $paymentResult);
    }
}