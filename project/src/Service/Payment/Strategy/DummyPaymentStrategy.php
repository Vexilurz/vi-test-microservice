<?php


namespace App\Service\Payment\Strategy;


use App\Entity\Order;

class DummyPaymentStrategy implements PaymentStrategyInterface
{
    public function pay(Order $order): bool
    {
        // dummy order payment process
        return true;
    }
}