<?php

namespace App\Tests\Order;

use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderPayTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/pay');
    }

    public function testPay(): void
    {
        $this->setBody(['orderId'=>2]);
        $this->checkResponseWithMessage('order has been paid');
    }

    public function testPaidAlready(): void
    {
        $this->setBody(['orderId'=>1]);
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('order is paid already');
    }

    public function testPayNotOwner(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId'=>1]);
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
