<?php


namespace App\Service\Payment;


use App\Service\Payment\Strategy\DummyPaymentStrategy;
use App\Service\Payment\Strategy\PaymentStrategyInterface;

class DummyPaymentService extends AbstractPaymentService
{
    protected function getNewPaymentStrategy(): PaymentStrategyInterface
    {
        return new DummyPaymentStrategy();
    }

}