<?php

namespace App\Tests\Product;

use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductGetFromOrderTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/product/get_from_order');
    }

    public function testGetAllProductsFromOrder(): void
    {
        $this->addToUrl("/1");
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 2);
        $expectedProducts = ['Microphone', 'Guitar'];
        foreach ($this->getResponseJson() as $product) {
            self::assertContains($product['name'], $expectedProducts);
        }
    }

    public function testGetAvailableProductsFromOrder(): void
    {
        $this->addToUrl("/1?available=1");
        $this->checkResponse();
        self::assertSame(count($this->getResponseJson()), 1);
        self::assertSame($this->getResponseJson()[0]['name'], 'Microphone');
    }

    public function testGetProductsFromWrongOrderId(): void
    {
        $this->addToUrl("/-1");
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
