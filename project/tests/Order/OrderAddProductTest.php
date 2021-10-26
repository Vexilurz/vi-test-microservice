<?php

namespace App\Tests\Order;

use App\Tests\VitmWithIdsWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderAddProductTest extends VitmWithIdsWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/add_product');
    }

    public function testAddProduct(): void
    {
        //TODO: same #2
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>$this->getFirstProductId() + 2]);
        $this->checkResponseWithMessage('product added to the order');
    }

    public function testAddProductNotOrderOwner(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>$this->getFirstProductId()]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testAddProductWrongOrderId(): void
    {
        $this->setBody(['orderId'=>-1, 'productId'=>$this->getFirstProductId()]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testAddProductWithoutOrderId(): void
    {
        $this->setBody(['productId'=>$this->getFirstProductId()]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testAddProductWrongProductId(): void
    {
        $this->setBody(['orderId'=>$this->getFirstOrderId(), 'productId'=>-1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testAddProductWithoutProductId(): void
    {
        $this->setBody(['orderId'=>$this->getFirstOrderId()]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testAddProductWithoutBodyData(): void
    {
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
