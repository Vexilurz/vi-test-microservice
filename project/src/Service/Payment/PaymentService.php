<?php

namespace App\Service\Payment;

use App\Entity\Order;
use App\Service\Payment\Strategy\PaymentStrategyInterface;
use Exception;

class PaymentService
{
    private PaymentStrategyInterface $paymentStrategy;

    public function __construct(PaymentStrategyInterface $paymentStrategy)
    {
        $this->paymentStrategy = $paymentStrategy;
    }

    public function payOrder(Order $order): bool
    {
        if ($order->getPaid()) {
            throw new Exception('order is paid already');
        }

        return $this->paymentStrategy->pay($order);
    }
}