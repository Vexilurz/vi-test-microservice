<?php

namespace App\Tests\Order;

use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderDeleteTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/delete');
    }

    public function testDeleteOrder(): void
    {
        $this->setBody(['orderId'=>1]);
        $this->checkResponseWithMessage('order deleted');
    }

    public function testNotOwnerDeleteOrder(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId'=>1]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testDeleteOrderWithWrongId(): void
    {
        $this->setBody(['orderId'=>-1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
