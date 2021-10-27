<?php

namespace App\Tests\Order;

use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderAddProductTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/add_product');
    }

    public function testAddProduct(): void
    {
        $this->setBody(['orderId'=>1, 'productId'=>3]);
        $this->checkResponseWithMessage('product added to the order');
    }

    public function testAddExistingProduct(): void
    {
        $this->setBody(['orderId'=>1, 'productId'=>1]);
        //TODO: refactor if will track adding existing products
        $this->checkResponseWithMessage('product added to the order');
    }

    public function testAddProductNotOrderOwner(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId'=>1, 'productId'=>1]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testAddProductWrongOrderId(): void
    {
        $this->setBody(['orderId'=>-1, 'productId'=>1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testAddProductWithoutOrderId(): void
    {
        $this->setBody(['productId'=>1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testAddProductWrongProductId(): void
    {
        $this->setBody(['orderId'=>1, 'productId'=>-1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testAddProductWithoutProductId(): void
    {
        $this->setBody(['orderId'=>1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testAddProductWithoutBodyData(): void
    {
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
