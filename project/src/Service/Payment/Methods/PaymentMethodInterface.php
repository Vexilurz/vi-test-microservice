<?php


namespace App\Service\Payment\Methods;


use App\Entity\Order;

interface PaymentMethodInterface
{
    public function payOrder(Order $order): bool;
}