<?php

namespace App\Tests\Order;

use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderRemoveProductTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/remove_product');
    }

    public function testRemoveProduct(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 1]);
        $this->checkResponseWithMessage('product removed from the order');
    }

    public function testRemoveProductNotInOrder(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 3]);
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('CHANGE IT');
    }

    public function testRemoveProductNotOrderOwner(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId' => 1, 'productId' => 1]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testRemoveProductWrongOrderId(): void
    {
        $this->setBody(['orderId' => -1, 'productId' => 1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testRemoveProductWithoutOrderId(): void
    {
        $this->setBody(['productId' => 1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testRemoveProductWrongProductId(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => -1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testRemoveProductWithoutProductId(): void
    {
        $this->setBody(['orderId' => 1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testRemoveProductWithoutBodyData(): void
    {
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
