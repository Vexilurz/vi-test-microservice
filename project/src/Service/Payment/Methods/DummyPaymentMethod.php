<?php


namespace App\Service\Payment\Methods;


use App\Entity\Order;

class DummyPaymentMethod implements PaymentMethodInterface
{
    public function payOrder(Order $order): bool
    {
        // dummy: order paid process
        return true;
    }
}