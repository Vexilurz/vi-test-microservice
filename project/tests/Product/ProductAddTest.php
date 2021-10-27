<?php

namespace App\Tests\Product;

use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductAddTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/product/add');
    }

    private function checkAddedProduct() {
        $this->checkResponseWithMessage('product added');
        self::assertArrayHasKey('productId', $this->getResponseJson());
    }

    public function testAdd(): void
    {
        $this->setBody(['name'=>'Foo','price'=>100500]);
        $this->checkAddedProduct();
    }

    public function testAddWithoutPrice(): void
    {
        $this->setBody(['name'=>'Foo']);
        $this->checkAddedProduct();
    }

    public function testAddWithoutName(): void
    {
        $this->setBody(['price'=>100500]);
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('name is empty');
    }

    public function testAddWithoutBodyData(): void
    {
        $this->setResponseCode(Response::HTTP_BAD_REQUEST);
        $this->checkResponseWithMessage('name is empty');
    }
}
