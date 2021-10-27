<?php

namespace App\Tests\Order;

use App\Tests\VitmBaseWebTestCase;

class OrderCreateTest extends VitmBaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setMethod('POST');
        $this->setUrl('/order/create');
    }

    public function testCreateOrder(): void
    {
        $this->checkResponseWithMessage('new order created');
        self::assertArrayHasKey('orderId', $this->getResponseJson());
    }
}
