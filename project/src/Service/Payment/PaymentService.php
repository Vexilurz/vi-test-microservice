<?php


namespace App\Service\Payment;


use App\Entity\Order;
use App\Service\Payment\Strategy\PaymentStrategyInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentService
{
    private PaymentStrategyInterface $paymentStrategy;

    public function __construct(PaymentStrategyInterface $paymentStrategy)
    {
        $this->paymentStrategy = $paymentStrategy;
    }

    public function payOrder(Order $order): bool {
        if ($order->getPaid())
        {
            throw new BadRequestHttpException('order is paid already');
        }

        return $this->paymentStrategy->pay($order);
    }
}