<?php

namespace App\Service\Payment\Strategy;

use App\Entity\Order;

interface PaymentStrategyInterface
{
    public function pay(Order $order): bool;
}