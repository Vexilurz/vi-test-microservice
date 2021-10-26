<?php

namespace App\Tests\Order;

use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderPayTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/pay');
    }

    public function testPay(): void
    {
        $this->setBody(['orderId'=>$this->getFirstOrderId()]);
        $this->checkResponseWithMessage('order has been paid');
    }

    public function testPayNotOwner(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId'=>$this->getFirstOrderId()]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testPayWrongOrderId(): void
    {
        $this->setBody(['orderId'=>-1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testPayWithoutOrderId(): void
    {
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
