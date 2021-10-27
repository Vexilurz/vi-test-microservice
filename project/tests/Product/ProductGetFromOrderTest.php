<?php

namespace App\Tests\Product;

use App\Tests\Traits\ProductCheckTrait;
use App\Tests\VitmBaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductGetFromOrderTest extends VitmBaseWebTestCase
{
    use ProductCheckTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUrl('/product/get_from_order');
    }

    public function testGetAllProductsFromOrder(): void
    {
        $this->addToUrl("/1");
        $this->checkResponse();
        $products = $this->getResponseJson();
        self::assertSame(count($products), 2);
        foreach ($products as $product) {
            self::assertTrue($this->checkProductInExpected($product, ['Microphone', 'Guitar']));
        }
    }

    public function testGetAvailableProductsFromOrder(): void
    {
        $this->addToUrl("/1?available=1");
        $this->checkResponse();
        $products = $this->getResponseJson();
        self::assertSame(count($products), 1);
        self::assertTrue($this->checkProductInExpected($products[0], ['Microphone']));
    }

    public function testGetProductsFromWrongOrderId(): void
    {
        $this->addToUrl("/-1");
        $this->setResponseCode(Response::HTTP_NOT_FOUND);
        $this->checkResponseWithMessage('order not found');
    }
}
