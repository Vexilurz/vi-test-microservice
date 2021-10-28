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

    public function testAddNotExistingProduct(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 3, 'productCount' => 2]);
        $this->checkResponseWithMessage('product added to the order');
        self::assertArrayHasKey('current_product_count', $this->getResponseJson());
        self::assertSame(2, $this->getResponseJson()['current_product_count']);
    }

    public function testAddExistingProduct(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 1, 'productCount' => 2]);
        $this->checkResponseWithMessage('product added to the order');
        self::assertArrayHasKey('current_product_count', $this->getResponseJson());
        self::assertSame(7, $this->getResponseJson()['current_product_count']);
    }

    public function testAddProductWithCountLessThan1(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 3, 'productCount' => 0]);
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('product count must be greater than zero');
    }

    public function testAddNotExistingProductWithoutCount(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 3]);
        $this->checkResponseWithMessage('product added to the order');
        self::assertArrayHasKey('current_product_count', $this->getResponseJson());
        self::assertSame(1, $this->getResponseJson()['current_product_count']);
    }

    public function testAddExistingProductWithoutCount(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => 1]);
        $this->checkResponseWithMessage('product added to the order');
        self::assertArrayHasKey('current_product_count', $this->getResponseJson());
        self::assertSame(6, $this->getResponseJson()['current_product_count']);
    }

    public function testAddProductNotOrderOwner(): void
    {
        $this->setNotOwnerApiToken();
        $this->setBody(['orderId' => 1, 'productId' => 1]);
        $this->setResponseCode(Response::HTTP_FORBIDDEN);
        $this->checkResponseWithMessage('user is not the owner of the order');
    }

    public function testAddProductWrongOrderId(): void
    {
        $this->setBody(['orderId' => -1, 'productId' => 1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testAddProductWithoutOrderId(): void
    {
        $this->setBody(['productId' => 1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }

    public function testAddProductWrongProductId(): void
    {
        $this->setBody(['orderId' => 1, 'productId' => -1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testAddProductWithoutProductId(): void
    {
        $this->setBody(['orderId' => 1]);
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('product not found');
    }

    public function testAddProductWithoutBodyData(): void
    {
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
