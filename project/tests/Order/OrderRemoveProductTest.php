<?php

namespace App\Tests\Order;

use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderRemoveProductTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/remove_product');
    }

    public function testRemoveProduct(): void
    {
        $this->setUrl('/order/add_product');
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>$this->getFirstProductId() + 2]);
        $this->checkResponseWithMessage('product added to the order');

        $this->setUrl('/order/remove_product');
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>$this->getFirstProductId() + 2]);
        $this->checkResponseWithMessage('product removed from the order');
    }

    public function testRemoveProductNotOrderOwner(): void
    {
        $this->setApiToken('test_token2');
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>$this->getFirstProductId()]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testRemoveProductWrongOrderId(): void
    {
        $this->setBody(['orderId'=>-1, 'productId'=>$this->getFirstProductId()]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testRemoveProductWithoutOrderId(): void
    {
        $this->setBody(['productId'=>$this->getFirstProductId()]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testRemoveProductWrongProductId(): void
    {
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>-1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testRemoveProductWithoutProductId(): void
    {
        $this->setBody(['orderId'=>$this->getFirstOrderId()]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testRemoveProductWithoutBodyData(): void
    {
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
