<?php


namespace App\Service\Payment;


use App\Service\Payment\Methods\DummyPaymentMethod;
use App\Service\Payment\Methods\PaymentMethodInterface;

class PaymentService
{
    private DummyPaymentMethod $dummyPaymentMethod;

    public function __construct(DummyPaymentMethod $dummyPaymentMethod)
    {
        $this->dummyPaymentMethod = $dummyPaymentMethod;
    }

    public function getPaymentMethod(): PaymentMethodInterface
    {
        // select payment method here
        return $this->dummyPaymentMethod;
    }
}