<?php

namespace App\Tests\Order;

use App\Entity\Order;
use App\Entity\User;
use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderDeleteTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/delete');
    }

    public function testDeleteOrder(): void
    {
        //TODO: same #1
        $this->setUrl('/order/create');
        $this->checkResponseWithMessage('new order created');
        self::assertArrayHasKey('id', $this->getResponseJson());
        $id = $this->getResponseJson()['id'];

        $this->setUrl('/order/delete');
        $this->setBody(['orderId'=>$id]);
        $this->checkResponseWithMessage('order deleted');
    }

    public function testNotOwnerDeleteOrder(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId'=>$this->getFirstOrderId()]);
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
